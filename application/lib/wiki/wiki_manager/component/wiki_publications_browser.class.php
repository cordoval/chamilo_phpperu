<?php
/**
 * $Id: wiki_publications_browser.class.php 210 2009-11-13 13:18:50Z kariboe $
 * @package application.lib.wiki.wiki_manager.component
 */

require_once dirname(__FILE__) . '/../wiki_manager.class.php';
require_once dirname(__FILE__) . '/../wiki_manager_component.class.php';
require_once dirname(__FILE__) . '/wiki_publication_browser/wiki_publication_browser_table.class.php';
require_once Path :: get_repository_path() . 'lib/complex_display/wiki/wiki_display.class.php';

/**
 * wiki component which allows the user to browse his wiki_publications
 * @author Sven Vanpoucke & Stefan Billiet
 */
class WikiManagerWikiPublicationsBrowserComponent extends WikiManagerComponent
{
    private $action_bar;

    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Wiki')));
        
        $this->display_header($trail);
        $this->action_bar = $this->get_toolbar();
        echo $this->action_bar->as_html();
        
        echo $this->get_table();
        $this->display_footer();
    }

    function get_table()
    {
        $table = new WikiPublicationBrowserTable($this, array(Application :: PARAM_APPLICATION => WikiManager :: APPLICATION_NAME, Application :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS), null);
        return $table->as_html();
    }

    function get_toolbar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishWiki'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_CREATE_WIKI_PUBLICATION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        /*$action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Browse'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_BROWSE_WIKIS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);*/
        
        //		if(!$this->introduction_text && PlatformSetting :: get('enable_introduction', 'weblcms'))
        //		{
        //			$action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //		}
        return $action_bar;
    }
}
?>