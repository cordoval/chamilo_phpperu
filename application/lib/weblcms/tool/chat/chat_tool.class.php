<?php
/**
 * $Id: chat_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.chat
 */
require_once dirname(__FILE__) . '/chat_tool_component.class.php';
/**
 * This tool allows a user to publish chatboxes in his or her course.
 */
class ChatTool extends Tool
{
    const ACTION_VIEW_CHAT = 'view';

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
            case self :: ACTION_VIEW_CHAT :
                $component = ChatToolComponent :: factory('Viewer', $this);
                break;
            case self :: ACTION_PUBLISH :
                $component = ChatToolComponent :: factory('Publisher', $this);
                break;
            default :
                $component = ChatToolComponent :: factory('Viewer', $this);
        }
        $component->run();
    }
}
?>