<?php
/**
 * $Id: course_settings_tool_component.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_settings
 */
require_once dirname(__FILE__) . '/../tool_component.class.php';
/**
==============================================================================
 *	This is the base class component for all announcement tool components.
 *
 *	@author Sven Vanpoucke
==============================================================================
 */

class CourseSettingsToolComponent extends ToolComponent
{

    static function factory($component_name, $announcement_tool)
    {
        return parent :: factory('CourseSettings', $component_name, $announcement_tool);
    }
}