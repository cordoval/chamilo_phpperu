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
    
    const ACTION_SUBSCRIBE = 'subscribe_browser';
    const ACTION_UNSUBSCRIBE = 'unsubscribe_browser';
    const ACTION_ADD_COURSE_GROUP = 'creator';
    const ACTION_EDIT_COURSE_GROUP = 'editor';
    const ACTION_DELETE_COURSE_GROUP = 'deleter';
    const ACTION_USER_SELF_SUBSCRIBE = 'self_subscriber';
    const ACTION_USER_SELF_UNSUBSCRIBE = 'self_unsubscriber';
    const ACTION_VIEW_GROUPS = 'browser';
    const ACTION_MANAGE_SUBSCRIPTIONS = 'manage_subscriptions';
    
    const PARAM_COURSE_GROUP = 'course_group';

    function CourseGroupTool($parent)
    {
        parent :: __construct($parent);
        $this->set_parameter(self :: PARAM_COURSE_GROUP, Request :: get(self :: PARAM_COURSE_GROUP));
        $this->parse_input_from_table();
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
        return self :: PARAM_ACTION;
    }
}
?>