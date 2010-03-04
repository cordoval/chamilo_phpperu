<?php
/**
 * $Id: course_group_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group
 */

require_once dirname(__FILE__) . '/course_group_tool_component.class.php';
/**
 * This tool allows a course_group to publish course_groups in his or her course.
 */
class CourseGroupTool extends Tool
{
    const PARAM_COURSE_GROUP_ACTION = 'tool_action';
    
    const ACTION_SUBSCRIBE = 'course_group_subscribe';
    const ACTION_UNSUBSCRIBE = 'course_group_unsubscribe';
    const ACTION_ADD_COURSE_GROUP = 'add_course_group';
    const ACTION_EDIT_COURSE_GROUP = 'edit_course_group';
    const ACTION_DELETE_COURSE_GROUP = 'delete_course_group';
    const ACTION_USER_SELF_SUBSCRIBE = 'user_subscribe';
    const ACTION_USER_SELF_UNSUBSCRIBE = 'user_unsubscribe';
    const ACTION_VIEW_GROUPS = 'view';
    const ACTION_MANAGE_SUBSCRIPTIONS = 'manage_subscriptions';
    
    const PARAM_COURSE_GROUP = 'course_group';

    /**
     * Inherited.
     */
    function run()
    {
        $action = $this->get_action();
        $component = parent :: run();
        
        if ($component)
            return;
        
        switch ($action)
        {
            case self :: ACTION_VIEW_GROUPS :
                $component = CourseGroupToolComponent :: factory('Browser', $this);
                break;
            case self :: ACTION_SUBSCRIBE :
                $component = CourseGroupToolComponent :: factory('SubscribeBrowser', $this);
                break;
            case self :: ACTION_UNSUBSCRIBE :
                $component = CourseGroupToolComponent :: factory('UnsubscribeBrowser', $this);
                break;
            case self :: ACTION_ADD_COURSE_GROUP :
                $component = CourseGroupToolComponent :: factory('Creator', $this);
                break;
            case self :: ACTION_EDIT_COURSE_GROUP :
                $component = CourseGroupToolComponent :: factory('Editor', $this);
                break;
            case self :: ACTION_DELETE_COURSE_GROUP :
                $component = CourseGroupToolComponent :: factory('Deleter', $this);
                break;
            case self :: ACTION_USER_SELF_SUBSCRIBE :
                $component = CourseGroupToolComponent :: factory('SelfSubscriber', $this);
                break;
            case self :: ACTION_USER_SELF_UNSUBSCRIBE :
                $component = CourseGroupToolComponent :: factory('SelfUnsubscriber', $this);
                break;
            case self :: ACTION_MANAGE_SUBSCRIPTIONS :
                $component = CourseGroupToolComponent :: factory('ManageSubscriptions', $this);
                break;
            default :
                $component = CourseGroupToolComponent :: factory('Browser', $this);
        }
        $component->run();
    }
    
    function get_course_group()
    {
    	$course_group_id = Request :: get(CourseGroupTool :: PARAM_COURSE_GROUP);
        $wdm = WeblcmsDataManager :: get_instance();
        return $wdm->retrieve_course_group($course_group_id);
    }
}
?>