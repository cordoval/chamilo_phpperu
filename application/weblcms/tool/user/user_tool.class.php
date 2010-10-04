<?php
/**
 * $Id: user_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.user.component
 */

require_once dirname(__FILE__) . '/component/subscribed_user_browser/subscribed_user_browser_table.class.php';
require_once dirname(__FILE__) . '/component/subscribe_group_browser/subscribe_group_browser_table.class.php';

/**
 * This tool allows a user to publish users in his or her course.
 */
class UserTool extends Tool
{
    const ACTION_SUBSCRIBE_USER_BROWSER = 'subscribe_browser';
    const ACTION_SUBSCRIBE_GROUP_BROWSER = 'group_subscribe_browser';
    const ACTION_UNSUBSCRIBE_USER_BROWSER = 'unsubscribe_browser';
    const ACTION_UNSUBSCRIBE = 'unsubscribe';
    const ACTION_SUBSCRIBE = 'subscribe';
    const ACTION_SUBSCRIBE_AS_ADMIN = 'subscribe_as_admin';
    const ACTION_SUBSCRIBE_GROUPS = 'group_subscribe';
    const ACTION_SUBSCRIBE_USERS_FROM_GROUP = 'group_users_subscribe';
    const ACTION_UNSUBSCRIBE_GROUPS = 'group_unsubscribe';
    const ACTION_REQUEST_SUBSCRIBE_USER = 'request_subscribe_user';
    const ACTION_USER_DETAILS = 'details';
    const ACTION_EMAIL = 'emailer';
    const ACTION_REPORTING = 'reporting_viewer';
    
    const DEFAULT_ACTION = self :: ACTION_UNSUBSCRIBE_USER_BROWSER;
    
    const PARAM_USERS = 'users';
    const PARAM_GROUPS = 'groups';
    const PARAM_STATUS = 'status';

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