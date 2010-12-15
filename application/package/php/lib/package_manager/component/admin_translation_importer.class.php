<?php

namespace application\package;

use common\libraries\WebApplication;
use common\libraries\Display;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\AdministrationComponent;

/**
 * @package application.package.package.component
 */
require_once WebApplication :: get_application_class_lib_path('package') . 'forms/translation_import_form.class.php';
require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/translation_importer/translation_importer.class.php';

/**
 * @author Sven Vanpoucke
 * @author
 */
class PackageManagerAdminTranslationImporterComponent extends PackageManager implements AdministrationComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$can_edit = PackageRights :: is_allowed(PackageRights :: EDIT_RIGHT, PackageRights :: LOCATION_VARIABLE_TRANSLATIONS, 'manager');
		$can_add = PackageRights :: is_allowed(PackageRights :: ADD_RIGHT, PackageRights :: LOCATION_VARIABLE_TRANSLATIONS, 'manager');

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
			$this->redirect(Translation :: get('TranslationsImported'), false, array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_ADMIN_IMPORT_TRANSLATIONS));
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
    	$breadcrumbtrail->add_help('package_admin_translation_importer');
    }
}
?>