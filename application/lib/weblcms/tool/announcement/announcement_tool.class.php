<?php
/**
 * $Id: announcement_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.announcement.component
 */

//require_once dirname(__FILE__) . '/announcement_tool_component.class.php';
/**
 * This tool allows a user to publish announcements in his or her course.
 */
class AnnouncementTool extends Tool
{
    const ACTION_VIEW_ANNOUNCEMENTS = 'view';

    /**
     * Inherited.
     */
    function run()
    {
        $action = $this->get_action();
    
        switch ($action)
        {
            case self :: ACTION_VIEW_ANNOUNCEMENTS :
                $component = $this->create_component('Viewer');
                break;
            case self :: ACTION_PUBLISH :
                $component = $this->create_component('Publisher');
                break;
            default :
                $component = $this->create_component('Viewer');
        }
        
        $component->run();
    }

    static function get_allowed_types()
    {
        return array(Announcement :: get_type_name());
    }

	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}
}
?>