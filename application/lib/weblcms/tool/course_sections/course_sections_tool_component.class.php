<?php
/**
 * $Id: course_sections_tool_component.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_sections
 */
require_once dirname(__FILE__) . '/../tool_component.class.php';
/**
==============================================================================
 *	This is the base class component for all announcement tool components.
 *
 *	@author Sven Vanpoucke
==============================================================================
 */

class CourseSectionsToolComponent extends ToolComponent
{

    static function factory($component_name, $course_sections_tool)
    {
        return parent :: factory('CourseSections', $component_name, $course_sections_tool);
    }
}