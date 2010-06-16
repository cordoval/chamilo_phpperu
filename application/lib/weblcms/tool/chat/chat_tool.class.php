<?php
/**
 * $Id: chat_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.chat
 */
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
             
        if ($component)
            return;
        
        switch ($action)
        {
            case self :: ACTION_VIEW_CHAT :
                $component = $this->create_component('Viewer');
                break;
            default :
                $component = $this->create_component('Viewer');
        }
        $component->run();
    }
    
	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}
}
?>