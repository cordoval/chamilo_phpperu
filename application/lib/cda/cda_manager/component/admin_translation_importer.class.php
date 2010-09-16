<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../../forms/translation_import_form.class.php';
require_once dirname(__FILE__) . '/translation_importer/translation_importer.class.php';

/**
 * @author Sven Vanpoucke
 * @author
 */
class CdaManagerAdminTranslationImporterComponent extends CdaManager implements AdministrationComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$can_edit = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, CdaRights :: LOCATION_VARIABLE_TRANSLATIONS, 'manager');
		$can_add = CdaRights :: is_allowed(CdaRights :: ADD_RIGHT, CdaRights :: LOCATION_VARIABLE_TRANSLATIONS, 'manager');

		if (!$can_edit && !$can_add)
		{
		    Display :: not_allowed();
		}

		$form = new TranslationImportForm($this, $this->get_url());

		if($form->validate())
		{
			$branch = $form->exportValue(LanguagePack :: PROPERTY_BRANCH);
			$file = Request :: file('file');

			$options = array(TranslationImporter :: OPTION_CREATE_NEW_LANGUAGE_PACKS => 1,
							 TranslationImporter :: OPTION_CREATE_NEW_LANGUAGES => 1,
							 TranslationImporter :: OPTION_CREATE_NEW_VARIABLES => 1);

			$importer = TranslationImporter :: factory($branch, $this->get_user(), $options);
			$importer->import($file);
			$this->redirect(Translation :: get('TranslationsImported'), false, array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_IMPORT_TRANSLATIONS));
		}
		else
		{
			$this->display_header();
			$form->display();
			$this->display_footer();
		}
	}
	
	function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('cda_admin_translation_importer');
    }
}
?>