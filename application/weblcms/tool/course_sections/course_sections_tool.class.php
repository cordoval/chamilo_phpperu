<?php
/**
 * $Id: course_sections_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_sections
 */

/**
 * This tool allows a user to publish course_sectionss in his or her course.
 */
class CourseSectionsTool extends Tool
{
    const ACTION_VIEW_COURSE_SECTIONS = 'viewer';
    const ACTION_CREATE_COURSE_SECTION = 'creator';
    const ACTION_REMOVE_COURSE_SECTION = 'deleter';
    const ACTION_UPDATE_COURSE_SECTION = 'updater';
    const ACTION_MOVE_COURSE_SECTION = 'mover';
    const ACTION_CHANGE_COURSE_SECTION_VISIBILITY = 'visibility_changer';
    const ACTION_SELECT_TOOLS_COURSE_SECTION = 'tool_selector';
    const ACTION_CHANGE_SECTION = 'change_section';
    
    const DEFAULT_ACTION = self :: ACTION_VIEW_COURSE_SECTIONS;
    
    const PARAM_COURSE_SECTION_ID = 'course_section_id';
    const PARAM_DIRECTION = 'direction';
    const PARAM_REMOVE_SELECTED = 'remove_selected';

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_ACTION;
    }

}
?>