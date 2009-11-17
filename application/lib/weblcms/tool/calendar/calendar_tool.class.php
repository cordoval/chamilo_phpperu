<?php
/**
 * $Id: calendar_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.calendar
 */
require_once Path :: get_repository_path() . 'lib/content_object/calendar_event/calendar_event.class.php';
require_once Path :: get_library_path() . 'utilities.class.php';
require_once dirname(__FILE__) . '/../../content_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/calendar_tool_component.class.php';
/**
 * This tool allows a user to publish events in his or her course.
 * There are 4 calendar views available:
 * - list view (chronological list of events)
 * - month view
 * - week view
 * - day view
 */
class CalendarTool extends Tool
{
    const ACTION_VIEW_CALENDAR = 'view';

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
            case self :: ACTION_VIEW_CALENDAR :
                $component = CalendarToolComponent :: factory('Viewer', $this);
                break;
            case self :: ACTION_PUBLISH :
                $component = CalendarToolComponent :: factory('Publisher', $this);
                break;
            default :
                $component = CalendarToolComponent :: factory('Viewer', $this);
        }
        $component->run();
    }

    static function get_allowed_types()
    {
        return array('calendar_event');
    }
}
?>