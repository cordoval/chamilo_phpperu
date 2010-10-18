<?php
/**
 * $Id: course_type_layout.class.php 216 2010-02-26 14:08:06Z Yannick & Tristan $
 * @package application.lib.weblcms.course_type
 */
require_once dirname(__FILE__) . '/../weblcms_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/course/course_layout.class.php';

class CourseTypeLayout extends CourseLayout
{
	
	const CLASS_NAME = __CLASS__;
	
	const PROPERTY_COURSE_TYPE_ID = 'course_type_id';
	const PROPERTY_INTRO_TEXT_FIXED = 'intro_text_visible_fixed';
	const PROPERTY_STUDENT_VIEW_FIXED = 'student_view_visible_fixed';
    const PROPERTY_LAYOUT_FIXED = 'layout_fixed';
    const PROPERTY_TOOL_SHORTCUT_FIXED = 'tool_shortcut_fixed';
    const PROPERTY_MENU_FIXED = 'menu_fixed';
    const PROPERTY_BREADCRUMB_FIXED = 'breadcrumb_fixed';
    const PROPERTY_FEEDBACK_FIXED = 'feedback_fixed';
    const PROPERTY_COURSE_CODE_VISIBLE_FIXED = 'course_code_visible_fixed';
    const PROPERTY_COURSE_MANAGER_NAME_VISIBLE_FIXED = 'course_manager_name_visible_fixed';
    const PROPERTY_COURSE_LANGUAGES_VISIBLE_FIXED = 'course_languages_visible_fixed';

    /**
     * inherited
     */
    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }
    
    /**
     * Get the default properties of all courses.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(
        	Array(self :: PROPERTY_COURSE_TYPE_ID,
        		  self :: PROPERTY_FEEDBACK_FIXED,
        		  self :: PROPERTY_LAYOUT_FIXED,
        		  self :: PROPERTY_TOOL_SHORTCUT_FIXED,
        		  self :: PROPERTY_MENU_FIXED,
        		  self :: PROPERTY_BREADCRUMB_FIXED,
        		  self :: PROPERTY_INTRO_TEXT_FIXED,
        		  self :: PROPERTY_STUDENT_VIEW_FIXED,
           		  self :: PROPERTY_COURSE_CODE_VISIBLE_FIXED,
        		  self :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE_FIXED,
        		  self :: PROPERTY_COURSE_LANGUAGES_VISIBLE_FIXED));
    }
    
	function get_course_type_id()
	{
		return $this->get_default_property(self :: PROPERTY_COURSE_TYPE_ID);
	}
    
    function get_intro_text_fixed()
    {
    	return $this->get_default_property(self :: PROPERTY_INTRO_TEXT_FIXED);
    }
    
    function get_student_view_fixed()
    {
    	return $this->get_default_property(self :: PROPERTY_STUDENT_VIEW_FIXED);
    }
    
    function get_layout_fixed()
    {
    	return $this->get_default_property(self :: PROPERTY_LAYOUT_FIXED);
    }
    
    function get_tool_shortcut_fixed()
    {
    	return $this->get_default_property(self :: PROPERTY_TOOL_SHORTCUT_FIXED);
    }
    
    function get_menu_fixed()
    {
    	return $this->get_default_property(self :: PROPERTY_MENU_FIXED);
    }
    
    function get_breadcrumb_fixed()
    {
    	return $this->get_default_property(self :: PROPERTY_BREADCRUMB_FIXED);
    }

    function get_feedback_fixed()
    {
    	return $this->get_default_property(self :: PROPERTY_FEEDBACK_FIXED);
    }
    
    function get_course_code_visible_fixed()
    {
    	return $this->get_default_property(self :: PROPERTY_COURSE_CODE_VISIBLE_FIXED);
    }
    
    function get_course_manager_name_visible_fixed()
    {
    	return $this->get_default_property(self :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE_FIXED);
    }
    
    function get_course_languages_visible_fixed()
	{
    	return $this->get_default_property(self :: PROPERTY_COURSE_LANGUAGES_VISIBLE_FIXED);
    }
    
	function set_course_type_id($course_type_id)
	{
		return $this->set_default_property(self :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
	}
    
    function set_feedback_fixed($feedback_fixed)
    {
    	$this->set_default_property(self :: PROPERTY_FEEDBACK_FIXED, $feedback_fixed);
    }
    
    function set_layout_fixed($layout_fixed)
    {
    	$this->set_default_property(self :: PROPERTY_LAYOUT_FIXED, $layout_fixed);
    }
    
    function set_tool_shortcut_fixed($tool_shortcut_fixed)
    {
    	$this->set_default_property(self :: PROPERTY_TOOL_SHORTCUT_FIXED, $tool_shortcut_fixed);
    }
    
    function set_menu_fixed($menu_fixed)
    {
    	$this->set_default_property(self :: PROPERTY_MENU_FIXED, $menu_fixed);
    }
    
    function set_breadcrumb_fixed($breadcrumb_fixed)
    {
    	$this->set_default_property(self :: PROPERTY_BREADCRUMB_FIXED, $breadcrumb_fixed);
    }
    
    function set_intro_text_fixed($intro_text_fixed)
    {
    	$this->set_default_property(self  :: PROPERTY_INTRO_TEXT_FIXED, $intro_text_fixed);
    }
    
    function set_student_view_fixed($student_view_fixed)
    {
    	$this->set_default_property(self :: PROPERTY_STUDENT_VIEW_FIXED, $student_view_fixed);
    }
    
    function set_course_code_visible_fixed($course_code_visible_fixed)
    {
    	$this->set_default_property(self :: PROPERTY_COURSE_CODE_VISIBLE_FIXED, $course_code_visible_fixed);
    }
    
    function set_course_manager_name_visible_fixed($course_manager_name_visible_fixed)
    {
    	$this->set_default_property(self :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE_FIXED, $course_manager_name_visible_fixed);
    }
    
    function set_course_languages_visible_fixed($course_languages_visible_fixed)
    {
    	$this->set_default_property(self :: PROPERTY_COURSE_LANGUAGES_VISIBLE_FIXED, $course_languages_visible_fixed);
    }
    
    function create()
    {
    	$wdm = WeblcmsDataManager :: get_instance();
		if (! $wdm->create_course_type_layout($this))
		{
			return false;
		}
		return true;
    }
    
	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
