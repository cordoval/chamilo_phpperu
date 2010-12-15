<?php

namespace application\package;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\WebApplication;
/**
 * @package application.package.package.component
 */

require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/translation_exporter/exporter_wizard.class.php';

/**
 * package component which allows the user to export translations
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageManagerTranslationExporterComponent extends PackageManager
{

	function run()
	{
		$wizard = new ExporterWizard($this);
		$wizard->run(); 
	}

	function display_header($extra_trail)
	{
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb($this->get_browse_package_languages_url(), Translation :: get('Package')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ExportTranslations')));
		$trail->merge($extra_trail);
		
		parent :: display_header($trail);
	}
	
	function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_browse_package_languages_url(), Translation :: get('PackageManagerPackageLanguagesBrowserComponent')));
    	$breadcrumbtrail->add_help('package_languages_importer');
    }
	
}
?>