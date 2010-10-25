<?php

require_once dirname(__FILE__) . '/maintenance_wizard_page.class.php';
require_once Path::get_repository_path() . '/lib/export/cp/cp_export.class.php';

/**
 * This form can be used to let the user select the file to import as well as additional properties required to import.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 *
 */
class CpImportSelectionMaintenanceWizardPage extends MaintenanceWizardPage
{
	const IMPORT_FILE_NAME = 'file_name';
	const CATEGORY_ID = 'category_id';

	function buildForm(){
		$this->add_select(self::CATEGORY_ID, Translation::get('CategoryTypeName'), $this->get_categories());

		$this->addElement('file', self::IMPORT_FILE_NAME, Translation::get('FileName'));
		$this->addRule(self::IMPORT_FILE_NAME, Translation::get('ThisFieldIsRequired'), 'required');

		$prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< ' . Translation :: get('Previous'));
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation::get('Import'));
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
		$this->_formBuilt = true;
	}

	/**
	 * Gets the categories defined in the user's repository.
	 * @return array The categories.
	 */
	function get_categories(){
		$categorymenu = new ContentObjectCategoryMenu($this->get_user_id(), 0);
		$renderer = new OptionsMenuRenderer();
		$categorymenu->render($renderer, 'sitemap');
		return $renderer->toArray();
	}

	function get_user_id(){
		return Session::get_user_id();
	}


}
?>