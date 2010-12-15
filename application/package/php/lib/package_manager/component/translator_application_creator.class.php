<?php

namespace application\package;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\WebApplication;
/**
 * @package application.package.package.component
 */
require_once WebApplication :: get_application_class_lib_path('package') . 'forms/translator_application_form.class.php';

/**
 * Component to create a new variable object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageManagerTranslatorApplicationCreatorComponent extends PackageManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$form = new TranslatorApplicationForm($this->get_url());

		if($form->validate())
		{
			$success = $form->create_application();
			$this->redirect($success ? Translation :: get('TranslatorApplicationCreated') : Translation :: get('TranslatorApplicationNotCreated'), !$success, 
				array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_PACKAGE_LANGUAGES));
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
    	$breadcrumbtrail->add_help('package_languages_application_creator');
    }
}
?>