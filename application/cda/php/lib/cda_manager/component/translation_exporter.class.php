<?php
/**
 * @package application.cda.cda.component
 */

require_once WebApplication :: get_application_class_lib_path('cda') . 'cda_manager/component/translation_exporter/exporter_wizard.class.php';

/**
 * cda component which allows the user to export translations
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerTranslationExporterComponent extends CdaManager
{

	function run()
	{
		$wizard = new ExporterWizard($this);
		$wizard->run(); 
	}

	function display_header($extra_trail)
	{
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb($this->get_browse_cda_languages_url(), Translation :: get('Cda')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ExportTranslations')));
		$trail->merge($extra_trail);
		
		parent :: display_header($trail);
	}
	
	function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_browse_cda_languages_url(), Translation :: get('CdaManagerCdaLanguagesBrowserComponent')));
    	$breadcrumbtrail->add_help('cda_languages_importer');
    }
	
}
?>