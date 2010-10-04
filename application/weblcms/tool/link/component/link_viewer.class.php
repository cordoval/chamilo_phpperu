<?php
/**
 * $Id: link_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.link.component
 */
require_once dirname(__FILE__) . '/../link_tool.class.php';
require_once dirname(__FILE__) . '/../link_tool_component.class.php';
require_once dirname(__FILE__) . '/link_viewer/link_browser.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/link/link.class.php';

class LinkToolViewerComponent extends LinkToolComponent
{
    private $action_bar;
    private $introduction_text;

    function run()
    {
        if (! $this->is_allowed(WeblcmsRights :: VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }

        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'link');

        $subselect_condition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Introduction :: get_type_name());
        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition, null, RepositoryDataManager :: get_instance());
        $condition = new AndCondition($conditions);

        $publications = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications($condition);
        $this->introduction_text = $publications->next_result();

        $this->action_bar = $this->get_action_bar();

        $browser = new LinkBrowser($this);
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('courses link tool');

        /*if(Request :: get('pcattree') != null)
        {
            foreach(Tool ::get_pcattree_parents(Request :: get('pcattree')) as $breadcrumb)
            {
                $trail->add(new Breadcrumb($this->get_url(), $breadcrumb->get_name()));
            }
        }*/
        if (Request :: get(Tool :: PARAM_PUBLICATION_ID) != null)
            $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => 'view', Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))), WebLcmsDataManager :: get_instance()->retrieve_content_object_publication(Request :: get(Tool :: PARAM_PUBLICATION_ID))->get_content_object()->get_title()));
        
        $html = $browser->as_html();    
            
        $this->display_header();

        //echo '<br /><a name="top"></a>';
        if (! Request :: get(Tool :: PARAM_PUBLICATION_ID))
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
    }

    function add_actionbar_item($item)
    {
        $this->action_bar->add_tool_action($item);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        if (! Request :: get(Tool :: PARAM_PUBLICATION_ID))
        {
            $action_bar->set_search_url($this->get_url());

            if ($this->is_allowed(WeblcmsRights :: ADD_RIGHT))
            {
                $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(LinkTool :: PARAM_ACTION => LinkTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
        }

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => null)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if (! Request :: get(Tool :: PARAM_PUBLICATION_ID) && $this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path() . 'action_category.png', $this->get_url(array(DocumentTool :: PARAM_ACTION => DocumentTool :: ACTION_MANAGE_CATEGORIES)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if (! $this->introduction_text && $this->get_course()->get_intro_text() && $this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path() . 'action_introduce.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        //$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(LinkTool :: PARAM_ACTION => LinkTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path().'action_delete.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));


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
}
?>