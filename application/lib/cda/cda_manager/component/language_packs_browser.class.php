<?php
/**
 * @package application.cda.cda.component
 */

require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';
require_once dirname(__FILE__).'/language_pack_browser/language_pack_browser_table.class.php';

/**
 * cda component which allows the user to browse his language_packs
 * @author Sven Vanpoucke
 * @author 
 */
class CdaManagerLanguagePacksBrowserComponent extends CdaManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_browse_cda_languages_url(), Translation :: get('BrowseLanguages')));
		$trail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_CDA_LANGUAGE => Request :: get(CdaManager :: PARAM_CDA_LANGUAGE))), Translation :: get('BrowseLanguagePacks')));

		$this->display_header($trail);

		echo $this->get_table();
		$this->display_footer();
	}

	function get_table()
	{
		$table = new LanguagePackBrowserTable($this, array(Application :: PARAM_APPLICATION => 'cda', Application :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_LANGUAGE_PACKS), null);
		return $table->as_html();
	}

}
?>