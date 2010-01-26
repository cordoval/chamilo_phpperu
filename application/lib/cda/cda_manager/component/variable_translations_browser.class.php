<?php
/**
 * @package application.cda.cda.component
 */

require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';
require_once dirname(__FILE__).'/variable_translation_browser/variable_translation_browser_table.class.php';

/**
 * cda component which allows the user to browse his variable_translations
 * @author Sven Vanpoucke
 * @author 
 */
class CdaManagerVariableTranslationsBrowserComponent extends CdaManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_browse_cda_languages_url(), Translation :: get('BrowseLanguages')));
		$trail->add(new Breadcrumb($this->get_browse_language_packs_url(), Translation :: get('BrowseLanguagePacks')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseVariableTranslations')));

		$this->display_header($trail);

		echo $this->get_table();
		$this->display_footer();
	}

	function get_table()
	{
		$table = new VariableTranslationBrowserTable($this, array(Application :: PARAM_APPLICATION => 'cda', Application :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_VARIABLE_TRANSLATIONS), null);
		return $table->as_html();
	}

}
?>