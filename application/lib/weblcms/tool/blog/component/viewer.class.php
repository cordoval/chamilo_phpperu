<?php
/**
 * $Id: blog_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.blog.component
 */
require_once dirname(__FILE__) . '/../../../content_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/blog_viewer/blog_browser.class.php';

class BlogToolViewerComponent extends BlogTool
{
    private $action_bar;

    function run()
    {
/*        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }*/
        $viewer = ToolComponent :: factory(ToolComponent :: ACTION_VIEW, $this);
        $viewer->run();
    }
}
        /*$pid = Request :: get(Tool :: PARAM_PUBLICATION_ID);

        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'blog');

        $subselect_condition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Introduction :: get_type_name());
        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition, null, RepositoryDataManager :: get_instance());
        $condition = new AndCondition($conditions);

        $publications = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications_new($condition);
        $this->introduction_text = $publications->next_result();

        $this->action_bar = $this->get_action_bar(Request :: get(Tool :: PARAM_PUBLICATION_ID));

        $browser = new BlogBrowser($this);
        $trail = new BreadcrumbTrail();
        $trail->add_help('courses blog tool');

        if ($browser->get_publication_category_tree() != null)
        {
            $breadcrumbs = $browser->get_publication_category_tree()->get_breadcrumbs();
            unset($breadcrumbs[0]);
            foreach ($breadcrumbs as $breadcrumb)
            {
                $trail->add(new Breadcrumb($breadcrumb['url'], $breadcrumb['title']));
            }
        }

        //needed when viewing a blog item, to access the breadcrumbs of the categories
        if (Request :: get('tool_action') == 'view' && Request :: get(Tool :: PARAM_PUBLICATION_ID) != null)
        {
            if (Request :: get('pcattree') != 0)
                $this->add_pcattree_breadcrumbs(Request :: get('pcattree'), $trail);
            $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => 'view', Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))), WebLcmsDataManager :: get_instance()->retrieve_content_object_publication(Request :: get(Tool :: PARAM_PUBLICATION_ID))->get_content_object()->get_title()));
        }
		
        $html = $browser->as_html();
        $this->display_header($trail, true);

        //echo '<br /><a name="top"></a>';
        //echo $this->perform_requested_actions();
        if (! isset($pid))
        {
            if ($this->get_course()->get_intro_text())
            {
                echo $this->display_introduction_text($this->introduction_text);
            }
        }
        
        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        echo $html;
        echo '</div>';

        $this->display_footer();
    }*/

/*    function add_actionbar_item($item)
    {
        $this->action_bar->add_tool_action($item);
    }

    function get_action_bar($pid)
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        if (! $pid)
        {
            $action_bar->set_search_url($this->get_url());

            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => AnnouncementTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => null)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if (! $this->introduction_text && $this->get_course()->get_intro_text() && $this->is_allowed(EDIT_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path() . 'action_introduce.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        if (! $pid && $this->is_allowed(EDIT_RIGHT))
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path() . 'action_category.png', $this->get_url(array(DocumentTool :: PARAM_ACTION => DocumentTool :: ACTION_MANAGE_CATEGORIES)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        //$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => AnnouncementTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path().'action_delete.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));


        if ($this->is_allowed(EDIT_RIGHT))
        {
            $action_bar->add_tool_action($this->get_access_details_toolbar_item($this));
        }

        return $action_bar;
    }

    function get_condition()
    {
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*');
            $conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            return new OrCondition($conditions);
        }

        return null;
    }

    private function add_pcattree_breadcrumbs($pcattree, &$trail)
    {
        $cat = WebLcmsDataManager :: get_instance()->retrieve_content_object_publication_category($pcattree);
        $categories[] = $cat;
        while ($cat->get_parent() != 0)
        {
            $cat = WebLcmsDataManager :: get_instance()->retrieve_content_object_publication_category($cat->get_parent());
            $categories[] = $cat;
        }
        $categories = array_reverse($categories);
        foreach ($categories as $categorie)
        {
            $trail->add(new Breadcrumb($this->get_url(array('pcattree' => $categorie->get_id())), $categorie->get_name()));
        }
    }
*/


?>