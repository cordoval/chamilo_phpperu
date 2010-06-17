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
    const ACTION_VIEW_COURSE_SECTIONS = 'view';
    const ACTION_CREATE_COURSE_SECTION = 'create';
    const ACTION_REMOVE_COURSE_SECTION = 'remove';
    const ACTION_UPDATE_COURSE_SECTION = 'update';
    const ACTION_MOVE_COURSE_SECTION = 'move_course_section';
    const ACTION_CHANGE_COURSE_SECTION_VISIBILITY = 'change_visibility';
    const ACTION_SELECT_TOOLS_COURSE_SECTION = 'tool_selector';
    const ACTION_CHANGE_SECTION = 'change_section';
    
    const PARAM_COURSE_SECTION_ID = 'course_section_id';
    const PARAM_DIRECTION = 'direction';
    const PARAM_REMOVE_SELECTED = 'remove_selected';

    /**
     * Inherited.
     */
    function run()
    {
        $action = $this->get_action();
        
        switch ($action)
        {
            case self :: ACTION_VIEW_COURSE_SECTIONS :
                $component = $this->create_component('Viewer');
                break;
            case self :: ACTION_CREATE_COURSE_SECTION :
                $component = $this->create_component('Creator');
                break;
            case self :: ACTION_REMOVE_COURSE_SECTION :
                $component = $this->create_component('Deleter');
                break;
            case self :: ACTION_UPDATE_COURSE_SECTION :
                $component = $this->create_component('Updater');
                break;
            case self :: ACTION_MOVE_COURSE_SECTION :
                $component = $this->create_component('Mover');
                break;
            case self :: ACTION_CHANGE_COURSE_SECTION_VISIBILITY :
                $component = $this->create_component('VisibilityChanger');
                break;
            case self :: ACTION_SELECT_TOOLS_COURSE_SECTION :
                $component = $this->create_component('ToolSelector');
                break;
            case self :: ACTION_CHANGE_SECTION :
                $component = $this->create_component('ChangeSection');
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
	function is_category_management_enabled()
	{
	    return false;
	}
	
}
?>