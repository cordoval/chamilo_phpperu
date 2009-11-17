<?php
/**
 * $Id: calendar_tool_component.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.calendar
 */
require_once dirname(__FILE__) . '/../tool_component.class.php';
/**
==============================================================================
 *	This is the base class component for all calendar tool components.
 *
 *	@author Sven Vanpoucke
==============================================================================
 */

class CalendarToolComponent extends ToolComponent
{

    static function factory($component_name, $calendar_tool)
    {
        return parent :: factory('Calendar', $component_name, $calendar_tool);
    }
}