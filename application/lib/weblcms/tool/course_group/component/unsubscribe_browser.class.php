<?php
/**
 * $Id: course_group_unsubscribe_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group.component
 */
require_once dirname(__FILE__) . '/../course_group_tool.class.php';
//require_once dirname(__FILE__) . '/../course_group_tool_component.class.php';
require_once dirname(__FILE__) . '/user_table/course_group_subscribed_user_browser_table.class.php';
require_once dirname(__FILE__) . '/user_table/course_group_unsubscribed_user_browser_table.class.php';

class CourseGroupToolUnsubscribeBrowserComponent extends CourseGroupTool
{
    private $action_bar;
    private $course_group;

    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }

        $course_group_id = Request :: get(CourseGroupTool :: PARAM_COURSE_GROUP);
        $wdm = WeblcmsDataManager :: get_instance();
       	$course_group = $wdm->retrieve_course_group($course_group_id);
       	$this->course_group = $course_group;

        $this->action_bar = $this->get_action_bar();
        $html[] = '<div style="clear: both;">&nbsp;</div>';
        
        if (Request :: get(WeblcmsManager :: PARAM_USERS))
        {
            $udm = UserDataManager :: get_instance();

            $users = Request :: get(WeblcmsManager :: PARAM_USERS);
            if(!is_array($users))
            {
            	$users = array($users);
            }

            foreach($users as $user)
            {
            	//$user = $udm->retrieve_user();
            	$course_group->unsubscribe_users($user);
            }

            $this->redirect(Translation :: get('UsersUnsubscribed'), false, array(Tool :: PARAM_ACTION => CourseGroupTool :: ACTION_UNSUBSCRIBE, CourseGroupTool :: PARAM_COURSE_GROUP => $course_group_id));

        }

        $table = new CourseGroupSubscribedUserBrowserTable($this, array(Application :: PARAM_APPLICATION => WeblcmsManager :: APPLICATION_NAME, Application :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE, WeblcmsManager :: PARAM_COURSE => $this->get_course()->get_id(),
        			WeblcmsManager :: PARAM_TOOL => $this->get_tool_id(), Tool :: PARAM_ACTION => CourseGroupTool :: ACTION_UNSUBSCRIBE, CourseGroupTool :: PARAM_COURSE_GROUP => $course_group_id), $this->get_condition());
        $html[] = $this->action_bar->as_html();

        $html[] = '<div class="clear"></div><div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_group.png);">';
        $html[] = '<div class="title">' . $course_group->get_name() . '</div>';
        $html[] = $course_group->get_description();
        $html[] = '<b>' . Translation :: get('NumberOfMembers') . ':</b> ' . $course_group->count_members();
        $html[] = '<br /><b>' . Translation :: get('MaximumMembers') . ':</b> ' . $course_group->get_max_number_of_members();
        $html[] = '<br /><b>' . Translation :: get('SelfRegistrationAllowed') . ':</b> ' . ($course_group->is_self_registration_allowed() ? Translation :: get('True') : Translation :: get('False'));
        $html[] = '<br /><b>' . Translation :: get('SelfUnRegistrationAllowed') . ':</b> ' . ($course_group->is_self_unregistration_allowed() ? Translation :: get('True') : Translation :: get('False'));
        $html[] = '</div>';

        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_users.png);">';
        $html[] = '<div class="title">' . Translation :: get('Users') . '</div>';
        $html[] = $table->as_html();
        $html[] = '</div>';
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => CourseGroupTool :: ACTION_UNSUBSCRIBE, CourseGroupTool :: PARAM_COURSE_GROUP => $course_group_id)), $course_group->get_name()));
        $trail->add_help('courses group');

        $this->display_header();
        echo implode($html, "\n");
        $this->display_footer();
    }

    function get_action_bar()
    {
        $course_group = $this->course_group;
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());
        $parameters[WeblcmsManager :: PARAM_COURSE_GROUP] = $course_group->get_id();
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url($parameters), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        $user = $this->get_parent()->get_user();

        $parameters = array();
        $parameters[WeblcmsManager :: PARAM_COURSE_GROUP] = $course_group->get_id();

        if (! $user->is_platform_admin())
        {
            if ($course_group->is_self_registration_allowed() && !$course_group->is_member($user))
            {
                $parameters = array();
                $parameters[WeblcmsManager :: PARAM_COURSE_GROUP] = $course_group->get_id();
                $parameters[CourseGroupTool :: PARAM_COURSE_GROUP_ACTION] = CourseGroupTool :: ACTION_USER_SELF_SUBSCRIBE;
                $subscribe_url = $this->get_url($parameters);

                $action_bar->add_common_action(new ToolbarItem(Translation :: get('SubscribeToGroup'), Theme :: get_common_image_path() . 'action_subscribe.png', $subscribe_url, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }

        	if ($course_group->is_self_unregistration_allowed() && $course_group->is_member($user))
            {
                $parameters = array();
                $parameters[WeblcmsManager :: PARAM_COURSE_GROUP] = $course_group->get_id();
                $parameters[CourseGroupTool :: PARAM_COURSE_GROUP_ACTION] = CourseGroupTool :: ACTION_USER_SELF_UNSUBSCRIBE;
                $unsubscribe_url = $this->get_url($parameters);

                $action_bar->add_common_action(new ToolbarItem(Translation :: get('UnSubscribeFromGroup'), Theme :: get_common_image_path() . 'action_unsubscribe.png', $unsubscribe_url, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
        }
        else
        {
            $parameters = array();
            $parameters[WeblcmsManager :: PARAM_COURSE_GROUP] = $course_group->get_id();
            $parameters[CourseGroupTool :: PARAM_COURSE_GROUP_ACTION] = CourseGroupTool :: ACTION_MANAGE_SUBSCRIPTIONS;
            $subscribe_url = $this->get_url($parameters);

            $action_bar->add_common_action(new ToolbarItem(Translation :: get('SubscribeUsers'), Theme :: get_common_image_path() . 'action_subscribe.png', $subscribe_url, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        //$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(CourseGroupTool :: PARAM_ACTION => CourseGroupTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        //$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path().'action_delete.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));


        return $action_bar;
    }

    function get_condition()
    {
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, '*' . $query . '*');
            $conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $query . '*');
            $conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $query . '*');
            return new OrCondition($conditions);
        }
    }

    function get_course_group()
    {
    	return $this->course_group;
    }

}
?>