<?php
/**
 * $Id: course_group_subscribe_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group.component
 */
require_once dirname(__FILE__) . '/../course_group_tool.class.php';
require_once dirname(__FILE__) . '/../course_group_tool_component.class.php';
require_once dirname(__FILE__) . '/user_table/course_group_subscribed_user_browser_table.class.php';
require_once dirname(__FILE__) . '/user_table/course_group_unsubscribed_user_browser_table.class.php';

class CourseGroupToolSubscribeBrowserComponent extends CourseGroupToolComponent
{
    private $action_bar;

    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $trail = new BreadcrumbTrail();
        $trail->add_help('courses group');
        
        $html = array();
        $this->display_header($trail, true);
        $html[] = '<div style="clear: both;">&nbsp;</div>';
        
        $this->action_bar = $this->get_action_bar();
        
        if (Request :: get(WeblcmsManager :: PARAM_USERS))
        {
            $udm = UserDataManager :: get_instance();
            $user = $udm->retrieve_user(Request :: get(WeblcmsManager :: PARAM_USERS));
            
            $course_group_id = Request :: get(CourseGroupTool :: PARAM_COURSE_GROUP);
        	$wdm = WeblcmsDataManager :: get_instance();
       	 	$course_group = $wdm->retrieve_course_group($course_group_id);
            
            $course_group->subscribe_users($user->get_id());
            $html[] = Display :: normal_message(Translation :: get('UserSubscribed'), true);
        }
        $table = new CourseGroupUnsubscribedUserBrowserTable($this->get_parent(), array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE, WeblcmsManager :: PARAM_COURSE => $this->get_course()->get_id(), WeblcmsManager :: PARAM_TOOL => $this->get_tool_id(), Tool :: PARAM_ACTION => CourseGroupTool :: ACTION_SUBSCRIBE), $this->get_condition());
        $html[] = $this->action_bar->as_html();
        $html[] = $table->as_html();
        echo implode($html, "\n");
        $this->display_footer();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        //$action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('UnSubscribeUsers'), Theme :: get_common_image_path() . 'action_unsubscribe.png', $this->get_url(array(CourseGroupTool :: PARAM_ACTION => CourseGroupTool :: ACTION_UNSUBSCRIBE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        //$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(CourseGroupTool :: PARAM_ACTION => CourseGroupTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path().'action_delete.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        

        return $action_bar;
    }

    function get_condition()
    {
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $conditions[] = new LikeCondition(User :: PROPERTY_USERNAME, $query);
            $conditions[] = new LikeCondition(User :: PROPERTY_FIRSTNAME, $query);
            $conditions[] = new LikeCondition(User :: PROPERTY_LASTNAME, $query);
            return new OrCondition($conditions);
        }
    }

}
?>