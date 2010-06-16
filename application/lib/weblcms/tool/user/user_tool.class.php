<?php
/**
 * $Id: user_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.user.component
 */

/**
 * This tool allows a user to publish users in his or her course.
 */
class UserTool extends Tool
{
    const ACTION_SUBSCRIBE_USERS = 'subscribe';
    const ACTION_SUBSCRIBE_GROUPS = 'subscribe_groups';
    const ACTION_UNSUBSCRIBE_USERS = 'unsubscribe';
    const ACTION_REQUEST_SUBSCRIBE_USER = 'request_subscribe';
    const ACTION_USER_DETAILS = 'user_details';
    const ACTION_EMAIL = 'email';

    /**
     * Inherited.
     */
    function run()
    {
        switch ($this->get_action())
        {
            case self :: ACTION_SUBSCRIBE_USERS :
                $component = $this->create_component('SubscribeBrowser');
                break;
            case self :: ACTION_UNSUBSCRIBE_USERS :
                $component = $this->create_component('UnsubscribeBrowser');
                break;
            case self :: ACTION_USER_DETAILS :
                $component = $this->create_component('Details');
                break;
            case self :: ACTION_SUBSCRIBE_GROUPS :
                $component = $this->create_component('GroupSubscribeBrowser');
                break;
           	case self :: ACTION_REQUEST_SUBSCRIBE_USER :
                $component = $this->create_component('RequestSubscribeUser');
                break;
            case self :: ACTION_EMAIL :
            	$component = $this->create_component('Emailer');
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