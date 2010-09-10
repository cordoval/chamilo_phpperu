<?php
/**
 * $Id: wiki_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki
 */

require_once dirname(__FILE__) . '/wiki_actionbar.class.php';
require_once dirname(__FILE__) . '/wiki_parser.class.php';
/**
 * This tool allows a user to publish wikis in his or her course.
 */
class WikiDisplay extends ComplexDisplay
{
    const PARAM_WIKI_ID = 'wiki_id';
    const PARAM_WIKI_PAGE_ID = 'wiki_page_id';
    
    const ACTION_BROWSE_WIKI = 'wiki_browser';
    const ACTION_VIEW_WIKI = 'viewer';
    const ACTION_VIEW_WIKI_PAGE = 'wiki_item_viewer';
    const ACTION_CREATE_PAGE = 'wiki_page_creator';
    const ACTION_SET_AS_HOMEPAGE = 'wiki_homepage_setter';
    const ACTION_DISCUSS = 'wiki_discuss';
    const ACTION_HISTORY = 'wiki_history';
    const ACTION_PAGE_STATISTICS = 'reporting_template_viewer';
    const ACTION_COMPARE = 'comparer';
    const ACTION_STATISTICS = 'reporting_template_viewer';
    const ACTION_ACCESS_DETAILS = 'reporting_template_viewer';
    const ACTION_VERSION_REVERT = 'version_reverter';
    const ACTION_VERSION_DELETE = 'version_deleter';
    
    const DEFAULT_ACTION = self :: ACTION_VIEW_WIKI;
    
    private $search_form;

    function WikiDisplay($parent)
    {
        parent :: __construct($parent);
        
        $this->search_form = new ActionBarSearchForm($this->get_url());
    }

    static function is_wiki_locked($wiki_id)
    {
        $wiki = RepositoryDataManager :: get_instance()->retrieve_content_object($wiki_id);
        return $wiki->get_locked() == 1;
    }

    static function get_wiki_homepage($wiki_id)
    {
        require_once Path :: get_repository_path() . '/lib/content_object/wiki_page/complex_wiki_page.class.php';
        $conditions[] = new EqualityCondition(ComplexWikiPage :: PROPERTY_PARENT, $wiki_id);
        $conditions[] = new EqualityCondition(ComplexWikiPage :: PROPERTY_IS_HOMEPAGE, 1, 'complex_wiki_page');
        $complex_wiki_homepage = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new AndCondition($conditions), array(), 0, 1, 'complex_wiki_page');
        return $complex_wiki_homepage->next_result();
    }

    public function get_toolbar($parent, $publish_id, $content_object, $selected_complex_content_object_item)
    {
        
        $action_bar = new WikiActionBar(WikiActionBar :: TYPE_WIKI);
        
        $action_bar->set_search_url($parent->get_url());
        
        //PAGE ACTIONS
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateWikiPage'), Theme :: get_common_image_path() . 'action_create.png', $parent->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_CREATE_PAGE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        if (! empty($selected_complex_content_object_item))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $parent->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_complex_content_object_item)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $parent->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_complex_content_object_item)), ToolbarItem :: DISPLAY_ICON_AND_LABEL, true));
            
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Discuss'), Theme :: get_common_image_path() . 'action_users.png', $parent->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_DISCUSS, 'wiki_publication' => Request :: get('wiki_publication'), ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_complex_content_object_item)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('BrowseWiki'), Theme :: get_common_image_path() . 'action_browser.png', $parent->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            
            //INFORMATION
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('History'), Theme :: get_common_image_path() . 'action_versions.png', $parent->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_HISTORY, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_complex_content_object_item)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            
            if ($this->get_parent()->is_allowed(EDIT_RIGHT))
            {
                $action_bar->add_tool_action(new ToolbarItem(Translation :: get('Statistics'), Theme :: get_common_image_path() . 'action_reporting.png', $parent->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_PAGE_STATISTICS, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_complex_content_object_item)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
        }
        else
        {
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('WikiStatistics'), Theme :: get_common_image_path() . 'action_reporting.png', $parent->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_STATISTICS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        $links = $content_object->get_links();
        
        //NAVIGATION
        if (! empty($links))
        {
            $p = new WikiParser($this, $publish_id, $links);
            $p->set_parent($this);
            $toolboxlinks = $p->handle_toolbox_links($links);
            $i = 0;
            
            foreach ($toolboxlinks as $title => $link)
            {
                /*if (substr_count($link, 'www.') == 1)
                {
                    $action_bar->add_navigation_link(new ToolbarItem(ucfirst($p->get_title_from_url($link)), null, $link, ToolbarItem :: DISPLAY_LABEL));
                    continue;
                }*/
                
                if (substr_count($link, 'class="does_not_exist"'))
                {
                    $action_bar->add_navigation_link(new ToolbarItem($title, null, $parent->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_CREATE_PAGE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL, null, 'does_not_exist'));
                }
                else
                {
                    $action_bar->add_navigation_link(new ToolbarItem($title, null, $parent->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI_PAGE, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $p->get_complex_id_from_url($link))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
                }
                $i ++;
            }
        }
        
        return $action_bar;
    }

    function get_breadcrumbtrail()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT)), $this->get_root_content_object()->get_title()));
        switch (Request :: get(ComplexDisplay :: PARAM_DISPLAY_ACTION))
        {
            case ComplexDisplay :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT :
                break;
            case WikiDisplay :: ACTION_CREATE_PAGE :
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_CREATE_PAGE)), Translation :: get('CreateWikiPage')));
                break;
            case WikiDisplay :: ACTION_UPDATE_CONTENT_OBJECT :
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_UPDATE_CONTENT_OBJECT)), Translation :: get('Edit')));
                break;
            case WikiDisplay :: ACTION_STATISTICS :
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_STATISTICS, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID))), Translation :: get('Reporting')));
                break;
            case WikiDisplay :: ACTION_ACCESS_DETAILS :
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_ACCESS_DETAILS, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID))), Translation :: get('Reporting')));
                break;
            case WikiDisplay :: ACTION_VIEW_WIKI_PAGE :
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI_PAGE, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID))), $this->get_content_object_from_complex_id(Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID))->get_title()));
                break;
            case WikiDisplay :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM :
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI_PAGE, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID))), $this->get_content_object_from_complex_id(Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID))->get_title()));
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID))), Translation :: get('Edit')));
                break;
            case WikiDisplay :: ACTION_DISCUSS :
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI_PAGE, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID))), $this->get_content_object_from_complex_id(Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID))->get_title()));
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => Request :: get(ComplexDisplay :: PARAM_DISPLAY_ACTION), ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID))), Translation :: get('Discuss')));
                break;
            case WikiDisplay :: ACTION_HISTORY :
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI_PAGE, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID))), $this->get_content_object_from_complex_id(Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID))->get_title()));
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => Request :: get(ComplexDisplay :: PARAM_DISPLAY_ACTION), ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID))), Translation :: get('History')));
                break;
            case WikiDisplay :: ACTION_PAGE_STATISTICS :
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI_PAGE, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID))), $this->get_content_object_from_complex_id(Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID))->get_title()));
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_PAGE_STATISTICS, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID))), Translation :: get('Reporting')));
                break;
        }
        return $trail;
    }

    private function get_content_object_from_complex_id($complex_id)
    {
        $complex_content_object_item = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($complex_id);
        return RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_object_item->get_ref());
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

    function get_publication()
    {
        return $this->get_parent()->get_publication();
    }

    function display_header(ComplexWikiPage $complex_wiki_page = null)
    {
        parent :: display_header();
        
        $html = array();
        
        // The general menu
        $html[] = '<div class="wiki-menu">';
        
        $html[] = '<div class="wiki-menu-section">';
        $toolbar = new Toolbar(Toolbar :: TYPE_VERTICAL);
        $toolbar->add_item(new ToolbarItem(Translation :: get('MainPage'), Theme :: get_common_image_path() . 'action_home.png', $this->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => null)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $toolbar->add_item(new ToolbarItem(Translation :: get('Contents'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_BROWSE_WIKI)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $toolbar->add_item(new ToolbarItem(Translation :: get('Statistics'), Theme :: get_common_image_path() . 'action_statistics.png', $this->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_STATISTICS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $html[] = $toolbar->as_html();
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        
        $html[] = '<div class="wiki-menu-section">';
        $toolbar = new Toolbar(Toolbar :: TYPE_VERTICAL);
        $toolbar->add_item(new ToolbarItem(Translation :: get('AddWikiPage'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_CREATE_PAGE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $html[] = $toolbar->as_html();
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        
        $html[] = '</div>';
        
        // The main content pane
        $html[] = '<div class="wiki-pane">';
        $html[] = '<div class="wiki-pane-actions-bar">';
        
        $display_action = $this->get_action();
        
        if ($display_action != self :: ACTION_CREATE_PAGE)
        {
            $html[] = '<ul class="wiki-pane-actions wiki-pane-actions-left">';
            
            if ($complex_wiki_page)
            {
                $read_url = $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI_PAGE, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page->get_id()));
                $discuss_url = $this->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_DISCUSS, 'wiki_publication' => Request :: get('wiki_publication'), ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page->get_id()));
                $statistics_url = $this->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_PAGE_STATISTICS, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page->get_id()));
                
                $html[] = '<li><a' . ($this->get_action() != self :: ACTION_DISCUSS && $this->get_action() != self :: ACTION_PAGE_STATISTICS ? ' class="current"' : '') . ' href="' . $read_url . '">' . Translation :: get('WikiArticle') . '</a></li>';
                $html[] = '<li><a' . ($this->get_action() == self :: ACTION_DISCUSS ? ' class="current"' : '') . ' href="' . $discuss_url . '">' . Translation :: get('WikiDiscuss') . '</a></li>';
                $html[] = '<li><a' . ($this->get_action() == self :: ACTION_PAGE_STATISTICS ? ' class="current"' : '') . ' href="' . $statistics_url . '">' . Translation :: get('WikiStatistics') . '</a></li>';
            }
            else
            {
                $html[] = '<li><a class="current" href="#">' . Translation :: get('WikiArticle') . '</a></li>';
            }
            
            $html[] = '</ul>';
            
            $html[] = '<div class="wiki-pane-actions wiki-pane-actions-right wiki-pane-search">';
            $html[] = $this->search_form->as_html();
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
            
            $html[] = '<ul class="wiki-pane-actions wiki-pane-actions-right">';
            
            if ($complex_wiki_page)
            {
                $delete_url = $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page->get_id()));
                $history_url = $this->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_HISTORY, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page->get_id()));
                $edit_url = $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page->get_id()));
                
                $html[] = '<li><a' . (in_array($this->get_action(), array(self :: ACTION_VIEW_WIKI, self :: ACTION_VIEW_WIKI_PAGE)) && ! Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID) ? ' class="current"' : '') . ' href="' . $read_url . '">' . Translation :: get('WikiRead') . '</a></li>';
                $html[] = '<li><a' . ($this->get_action() == self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM ? ' class="current"' : '') . ' href="' . $edit_url . '">' . Translation :: get('WikiEdit') . '</a></li>';
                $html[] = '<li><a' . (($this->get_action() == self :: ACTION_HISTORY) || ($this->get_action() == self :: ACTION_VIEW_WIKI_PAGE && Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID)) ? ' class="current"' : '') . ' href="' . $history_url . '">' . Translation :: get('WikiHistory') . '</a></li>';
                $html[] = '<li><a' . ($this->get_action() == self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM ? ' class="current"' : '') . ' href="' . $delete_url . '" onClick="return confirm(\'' . Translation :: get('DeleteQuestion') . '\')">' . Translation :: get('WikiDelete') . '</a></li>';
            }
            else
            {
                $html[] = '<li><a class="current" href="#">' . Translation :: get('WikiRead') . '</a></li>';
            }
            
            $html[] = '</ul>';
        }
        else
        {
            $html[] = '<ul class="wiki-pane-actions wiki-pane-actions-left">';
            $html[] = '<li><a class="current" href="#">' . Translation :: get('AddWikiPage') . '</a></li>';
            $html[] = '</ul>';
            
            $html[] = '<ul class="wiki-pane-actions wiki-pane-actions-right">';
            
            $repo_viewer_action = Request :: get(RepoViewer :: PARAM_ACTION);
            $creator_url = $this->get_url(array(RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_CREATOR));
            $browser_url = $this->get_url(array(RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_BROWSER));
            $html[] = '<li><a' . (($repo_viewer_action == RepoViewer :: ACTION_CREATOR || is_null($repo_viewer_action)) ? ' class="current"' : '') . ' href="' . $creator_url . '">' . Translation :: get('WikiPageNew') . '</a></li>';
            $html[] = '<li><a' . ($repo_viewer_action == RepoViewer :: ACTION_BROWSER ? ' class="current"' : '') . ' href="' . $browser_url . '">' . Translation :: get('WikiPageSelect') . '</a></li>';
            
            if ($repo_viewer_action == RepoViewer :: ACTION_VIEWER)
            {
                $html[] = '<li><a class="current" href="#">' . Translation :: get('WikiPagePreview') . '</a></li>';
            }
            
            $html[] = '</ul>';
        }
        
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        
        $html[] = '<div class="wiki-pane-content">';
        
        echo implode("\n", $html);
    }

    function display_footer()
    {
        $html = array();
        
        $html[] = '<div class="clear"></div>';
        $html[] = '<div class="wiki-pane-top"><a href=#top>' . Theme :: get_common_image('action_ajax_add', 'png', Translation :: get('BackToTop')) . '</a></div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        
        echo implode("\n", $html);
        
        parent :: display_footer();
    }

    function get_search_form()
    {
        return $this->search_form;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_DISPLAY_ACTION;
    }
}
?>