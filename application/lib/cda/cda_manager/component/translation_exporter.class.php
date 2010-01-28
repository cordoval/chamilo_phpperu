<?php
/**
 * @package application.cda.cda.component
 */

require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';
require_once dirname(__FILE__).'/translation_exporter/exporter_wizard.class.php';

/**
 * cda component which allows the user to export translations
 * @author Sven Vanpoucke
 * @author
 */
class CdaManagerTranslationExporterComponent extends CdaManagerComponent
{

	function run()
	{
		$wizard = new ExporterWizard($this);
		$wizard->run(); 
	}

	function display_header($extra_trail)
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_CDA_LANGUAGES)), Translation :: get('BrowseLanguages')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ExportTranslations')));
		$trail->merge($extra_trail);
		
		parent :: display_header($trail);
	}
	
}
?>