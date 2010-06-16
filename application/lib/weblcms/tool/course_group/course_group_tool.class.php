<?php
/**
 * $Id: course_group_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group
 */

require_once dirname(__FILE__) . '/component/course_group_table/course_group_table.class.php';
/**
 * This tool allows a course_group to publish course_groups in his or her course.
 */
class CourseGroupTool extends Tool
{
    const PARAM_COURSE_GROUP_ACTION = 'tool_action';
    const PARAM_DELETE_COURSE_GROUPS = 'delete_course_groups';
    const PARAM_UNSUBSCRIBE_USERS = 'unsubscribe_users';
    
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

    function CourseGroupTool($parent)
    {
    	parent :: __construct($parent);
    	$this->parse_input_from_table();
    }
    
    /**
     * Inherited.
     */
    function run()
    {
        $action = $this->get_action();
//        $component = parent :: run();
//        
//        if ($component)
//            return;
        
        switch ($action)
        {
            case self :: ACTION_VIEW_GROUPS :
                $component = $this->create_component('Browser');
//            	$component = CourseGroupToolComponent :: factory('Browser', $this);
                break;
            case self :: ACTION_SUBSCRIBE :
                $component = $this->create_component('SubscribeBrowser');
//            	$component = CourseGroupToolComponent :: factory('SubscribeBrowser', $this);
                break;
            case self :: ACTION_UNSUBSCRIBE :
                $component = $this->create_component('UnsubscribeBrowser');
//            	$component = CourseGroupToolComponent :: factory('UnsubscribeBrowser', $this);
                break;
            case self :: ACTION_ADD_COURSE_GROUP :
                $component = $this->create_component('Creator');
//            	$component = CourseGroupToolComponent :: factory('Creator', $this);
                break;
            case self :: ACTION_EDIT_COURSE_GROUP :
                $component = $this->create_component('Editor');
//            	$component = CourseGroupToolComponent :: factory('Editor', $this);
                break;
            case self :: ACTION_DELETE_COURSE_GROUP :
                $component = $this->create_component('Deleter');
//            	$component = CourseGroupToolComponent :: factory('Deleter', $this);
                break;
            case self :: ACTION_USER_SELF_SUBSCRIBE :
                $component = $this->create_component('SelfSubscriber');
//            	$component = CourseGroupToolComponent :: factory('SelfSubscriber', $this);
                break;
            case self :: ACTION_USER_SELF_UNSUBSCRIBE :
                $component = $this->create_component('SelfUnsubscriber');
//            	$component = CourseGroupToolComponent :: factory('SelfUnsubscriber', $this);
                break;
            case self :: ACTION_MANAGE_SUBSCRIPTIONS :
                $component = $this->create_component('ManageSubscriptions');
//            	$component = CourseGroupToolComponent :: factory('ManageSubscriptions', $this);
                break;
            default :
                $component = $this->create_component('Browser');
//            	$component = CourseGroupToolComponent :: factory('Browser', $this);
        }
        $component->run();
    }
    
	private function parse_input_from_table()
    {
        if (isset($_POST['action']))
        {
            $ids = $_POST[CourseGroupTable :: DEFAULT_NAME . CourseGroupTable :: CHECKBOX_NAME_SUFFIX];
            
            if (empty($ids))
            {
            	$ids = array();
            }
            elseif (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            $action = $_POST['action'];
            switch ($action)
            {
                case self :: PARAM_DELETE_COURSE_GROUPS :
                    $this->set_action(self :: ACTION_DELETE_COURSE_GROUP);
                    Request :: set_get(self :: PARAM_COURSE_GROUP, $ids);
                    break;
                case self :: PARAM_UNSUBSCRIBE_USERS :
                	$this->set_action(self :: ACTION_UNSUBSCRIBE);
                	Request :: set_get(WeblcmsManager :: PARAM_USERS, $ids);
                    break;
            }
        }
    }
    
    function get_course_group()
    {
    	$course_group_id = Request :: get(CourseGroupTool :: PARAM_COURSE_GROUP);
        $wdm = WeblcmsDataManager :: get_instance();
        return $wdm->retrieve_course_group($course_group_id);
    }
    
	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}
}
?>