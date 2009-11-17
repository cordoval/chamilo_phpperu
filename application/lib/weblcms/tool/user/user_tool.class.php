<?php
/**
 * $Id: user_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.user.component
 */

require_once dirname(__FILE__) . '/user_tool_component.class.php';
/**
 * This tool allows a user to publish users in his or her course.
 */
class UserTool extends Tool
{
    const ACTION_SUBSCRIBE_USERS = 'subscribe';
    const ACTION_SUBSCRIBE_GROUPS = 'subscribe_groups';
    const ACTION_UNSUBSCRIBE_USERS = 'unsubscribe';
    const ACTION_USER_DETAILS = 'user_details';

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
            case self :: ACTION_SUBSCRIBE_USERS :
                $component = UserToolComponent :: factory('SubscribeBrowser', $this);
                break;
            case self :: ACTION_UNSUBSCRIBE_USERS :
                $component = UserToolComponent :: factory('UnsubscribeBrowser', $this);
                break;
            case self :: ACTION_USER_DETAILS :
                $component = UserToolComponent :: factory('Details', $this);
                break;
            case self :: ACTION_SUBSCRIBE_GROUPS :
                $component = UserToolComponent :: factory('GroupSubscribeBrowser', $this);
                break;
            default :
                $component = UserToolComponent :: factory('UnsubscribeBrowser', $this);
        }
        $component->run();
    }
}
?>