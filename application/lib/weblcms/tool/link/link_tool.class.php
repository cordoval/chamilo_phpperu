<?php
/**
 * $Id: link_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.link
 */

/**
 * This tool allows a user to publish links in his or her course.
 */
class LinkTool extends Tool
{
    const ACTION_VIEW_ANNOUNCEMENTS = 'view';

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
            case self :: ACTION_VIEW_ANNOUNCEMENTS :
                $component = LinkToolComponent :: factory('Viewer', $this);
                break;
            case self :: ACTION_PUBLISH :
                $component = LinkToolComponent :: factory('Publisher', $this);
                break;
            default :
                $component = LinkToolComponent :: factory('Viewer', $this);
        }
        $component->run();
    }

    static function get_allowed_types()
    {
        return array(Link :: get_type_name());
    }
    
	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}
}
?>