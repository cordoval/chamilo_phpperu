<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';

/**
 * Cda component which allows the user to browse the cda application
 * @author Sven Vanpoucke
 * @author 
 */
class CdaManagerBrowserComponent extends CdaManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseCda')));

		$this->display_header($trail);

		echo '<br /><a href="' . $this->get_browse_cda_languages_url() . '">' . Translation :: get('BrowseCdaLanguages') . '</a>';
		echo '<br /><a href="' . $this->get_browse_language_packs_url() . '">' . Translation :: get('BrowseLanguagePacks') . '</a>';
		echo '<br /><a href="' . $this->get_browse_variables_url() . '">' . Translation :: get('BrowseVariables') . '</a>';
		echo '<br /><a href="' . $this->get_browse_variable_translations_url() . '">' . Translation :: get('BrowseVariableTranslations') . '</a>';

		$this->display_footer();
	}

}
?>