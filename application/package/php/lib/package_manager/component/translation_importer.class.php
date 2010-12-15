<?php

namespace application\package;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\WebApplication;
use common\libraries\Utilities;
/**
 * @package application.package.package.component
 */

require_once WebApplication :: get_application_class_lib_path('package') . 'forms/translation_import_form.class.php';
require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/translation_importer/translation_importer.class.php';

/**
 * @author Sven Vanpoucke
 * @author 
 */
class PackageManagerTranslationImporterComponent extends PackageManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$form = new TranslationImportForm($this, $this->get_url());

		if($form->validate())
		{
			$branch = $form->exportValue(LanguagePack :: PROPERTY_BRANCH);
			$file = Request :: file('file');
			
			$options = array(TranslationImporter :: OPTION_CREATE_NEW_LANGUAGE_PACKS => 0, 
							 TranslationImporter :: OPTION_CREATE_NEW_LANGUAGES => 0,
							 TranslationImporter :: OPTION_CREATE_NEW_VARIABLES => 0);
			
			$importer = TranslationImporter :: factory($branch, $this->get_user(), $options);
			$importer->import($file);

			$this->redirect(Translation :: get('ObjectImported', array('OBJECT' => Translation :: get('Translations')), Utilities :: COMMON_LIBRARIES), false, array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_PACKAGE_LANGUAGES));
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
    	$breadcrumbtrail->add(new Breadcrumb($this->get_browse_package_languages_url(), Translation :: get('PackageManagerPackageLanguagesBrowserComponent')));
    	$breadcrumbtrail->add_help('package_languages_importer');
    }
}
?>