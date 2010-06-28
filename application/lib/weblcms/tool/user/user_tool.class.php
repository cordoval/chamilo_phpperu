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
    const ACTION_SUBSCRIBE_GROUP_BROWSER = 'subscribe_groups_browser';
    const ACTION_UNSUBSCRIBE_USER_BROWSER = 'unsubscribe_browser';
	const ACTION_UNSUBSCRIBE = 'unsubscribe';
	const ACTION_SUBSCRIBE = 'subscribe';
	const ACTION_SUBSCRIBE_AS_ADMIN = 'subscribe_as_admin';    
	const ACTION_SUBSCRIBE_GROUPS = 'subscribe_groups';
	const ACTION_SUBSCRIBE_USERS_FROM_GROUP = 'subscribe_group_users';
	const ACTION_UNSUBSCRIBE_GROUPS = 'unsubscribe_groups';
    const ACTION_REQUEST_SUBSCRIBE_USER = 'request_subscribe';
    const ACTION_USER_DETAILS = 'user_details';
    const ACTION_EMAIL = 'email';
    const ACTION_REPORTING = 'reporting';
    
    const PARAM_USERS = 'users';
    const PARAM_GROUPS = 'groups';
    const PARAM_STATUS = 'status';

    /**
     * Inherited.
     */
    function run()
    {
        switch ($this->get_action())
        {
            case self :: ACTION_SUBSCRIBE_USER_BROWSER :
                $component = $this->create_component('SubscribeBrowser');
                break;
            case self :: ACTION_UNSUBSCRIBE_USER_BROWSER :
                $component = $this->create_component('UnsubscribeBrowser');
                break;
            case self :: ACTION_USER_DETAILS :
                $component = $this->create_component('Details');
                break;
            case self :: ACTION_SUBSCRIBE_GROUP_BROWSER :
                $component = $this->create_component('GroupSubscribeBrowser');
                break;
           	case self :: ACTION_REQUEST_SUBSCRIBE_USER :
                $component = $this->create_component('RequestSubscribeUser');
                break;
            case self :: ACTION_EMAIL :
            	$component = $this->create_component('Emailer');
            	break;
            case self :: ACTION_PUBLISH_INTRODUCTION:
            	$component = $this->create_component('IntroductionPublisher');
                break;
            case self :: ACTION_UNSUBSCRIBE:
            	$component = $this->create_component('Unsubscribe');
                break;
            case self :: ACTION_SUBSCRIBE:
            	$component = $this->create_component('Subscribe');
                break;
            case self :: ACTION_SUBSCRIBE_AS_ADMIN:
            	$component = $this->create_component('Subscribe');
            	Request :: set_get(self :: PARAM_STATUS, 1);
                break;
            case self :: ACTION_SUBSCRIBE_GROUPS:
            	$component = $this->create_component('GroupSubscribe');
                break;
            case self :: ACTION_SUBSCRIBE_USERS_FROM_GROUP:
            	$component = $this->create_component('GroupUsersSubscribe');
                break;
            case self :: ACTION_UNSUBSCRIBE_GROUPS:
            	$component = $this->create_component('GroupUnsubscribe');
                break;
            default :
                $component = $this->create_component('UnsubscribeBrowser');
        }
        $component->run();
    }
    
	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}
}
?>