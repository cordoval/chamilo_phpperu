<?php
/**
 * $Id: course_layout.class.php 216 2009-11-13 14:08:06Z Yannick & Tristan $
 * @package application.lib.weblcms.course
 */
require_once dirname(__FILE__) . '/../weblcms_data_manager.class.php';
/**
 * This class represents a course_layout for a cours in the weblcms.
 *
 * To access the values of the properties, this class and its subclasses should
 * provide accessor methods. The names of the properties should be defined as
 * class constants, for standardization purposes. It is recommended that the
 * names of these constants start with the string "PROPERTY_".
 *
 */
class CourseLayout extends DataClass
{

	const CLASS_NAME = __CLASS__;
	
	const PROPERTY_COURSE_ID = "course_id";
	const PROPERTY_INTRO_TEXT = "intro_text_visible";
	const PROPERTY_STUDENT_VIEW = "student_view_visible";
    const PROPERTY_LAYOUT = "layout";
    const PROPERTY_TOOL_SHORTCUT = 'tool_shortcut';
    const PROPERTY_MENU = 'menu';
    const PROPERTY_BREADCRUMB = 'breadcrumb';
    const PROPERTY_FEEDBACK = "feedback";
    const PROPERTY_COURSE_CODE_VISIBLE = "course_code_visible";
    const PROPERTY_COURSE_MANAGER_NAME_VISIBLE = "course_manager_name_visible";
    const PROPERTY_COURSE_LANGUAGES_VISIBLE = "course_languages_visible";

    const LAYOUT_2_COLUMNS = 1;
    const LAYOUT_3_COLUMNS = 2;
    const LAYOUT_2_COLUMNS_GROUP_INACTIVE = 3;
    const LAYOUT_3_COLUMNS_GROUP_INACTIVE = 4;

    const TOOL_SHORTCUT_OFF = 1;
    const TOOL_SHORTCUT_ON = 2;

    const MENU_OFF = 1;
    const MENU_LEFT_ICON = 2;
    const MENU_LEFT_ICON_TEXT = 3;
    const MENU_LEFT_TEXT = 4;
    const MENU_RIGHT_ICON = 5;
    const MENU_RIGHT_ICON_TEXT = 6;
    const MENU_RIGHT_TEXT = 7;

    const BREADCRUMB_TITLE = 1;
    const BREADCRUMB_CODE = 2;
    const BREADCRUMB_COURSE_HOME = 3;
    
    /**
     * inherited
     */
    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }
    
    static function get_layouts()
    {
        return array(self :: LAYOUT_2_COLUMNS => Translation :: get('TwoColumns'), self :: LAYOUT_3_COLUMNS => Translation :: get('ThreeColumns'), self :: LAYOUT_2_COLUMNS_GROUP_INACTIVE => Translation :: get('TwoColumnsGroupInactive'), self :: LAYOUT_3_COLUMNS_GROUP_INACTIVE => Translation :: get('ThreeColumnsGroupInactive'));
    }

    static function get_tool_shortcut_options()
    {
        return array(self :: TOOL_SHORTCUT_OFF => Translation :: get('Off'), self :: TOOL_SHORTCUT_ON => Translation :: get('On'));
    }

    static function get_menu_options()
    {
        return array(self :: MENU_OFF => Translation :: get('Off'), self :: MENU_LEFT_ICON => Translation :: get('LeftIcon'), self :: MENU_LEFT_ICON_TEXT => Translation :: get('LeftIconText'), self :: MENU_LEFT_TEXT => Translation :: get('LeftText'), self :: MENU_RIGHT_ICON => Translation :: get('RightIcon'), self :: MENU_RIGHT_ICON_TEXT => Translation :: get('RightIconText'), self :: MENU_RIGHT_TEXT => Translation :: get('RightText'));
    }

    static function get_breadcrumb_options()
    {
        return array(self :: BREADCRUMB_TITLE => Translation :: get('Title'), self :: BREADCRUMB_CODE => Translation :: get('Code'), self :: BREADCRUMB_COURSE_HOME => Translation :: get('CourseHome'));
    }
    
    static function get_title($course)
    {
        switch ($course->get_breadcrumb())
        {
            case CourseLayout :: BREADCRUMB_TITLE :
                return $course->get_name();
                break;
            case CourseLayout :: BREADCRUMB_CODE :
                return $course->get_visual();
                break;
            case CourseLayout :: BREADCRUMB_COURSE_HOME :
                return Translation :: get('CourseHome');
                break;
            default :
                return $course->get_visual();
                break;
        }
    }
    /**
     * Get the default properties of all courses.
     * @return array The property names.
     */
    static function get_default_property_names($extended_property_names = array())
    {
        if(empty($extended_property_names)) $extended_property_names = array(self :: PROPERTY_COURSE_ID);
        return array_merge($extended_property_names,
        	array(self :: PROPERTY_FEEDBACK,
        		  self :: PROPERTY_LAYOUT,
        		  self :: PROPERTY_TOOL_SHORTCUT,
        		  self :: PROPERTY_MENU,
        		  self :: PROPERTY_BREADCRUMB,
        		  self :: PROPERTY_INTRO_TEXT,
        		  self :: PROPERTY_STUDENT_VIEW,
        		  self :: PROPERTY_COURSE_CODE_VISIBLE,
        		  self :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE,
        		  self :: PROPERTY_COURSE_LANGUAGES_VISIBLE));
    }
    
    /*
     * Getters
     */
    
    function get_course_id()
    {
    	return $this->get_default_property(self :: PROPERTY_COURSE_ID);
    }
    
    function get_intro_text()
    {
        return $this->get_default_property(self :: PROPERTY_INTRO_TEXT);
    }
    
    function get_student_view()
    {
        return $this->get_default_property(self :: PROPERTY_STUDENT_VIEW);
    }
    
    function get_layout()
    {
        return $this->get_default_property(self :: PROPERTY_LAYOUT);
    }
    
    function get_tool_shortcut()
    {
        return $this->get_default_property(self :: PROPERTY_TOOL_SHORTCUT);
    }
    
 	function get_menu()
    {
        return $this->get_default_property(self :: PROPERTY_MENU);
    }
    
    function get_breadcrumb()
    {
        return $this->get_default_property(self :: PROPERTY_BREADCRUMB);
    }
    
    function get_feedback()
    {
        return $this->get_default_property(self :: PROPERTY_FEEDBACK);
    }
    
    function get_course_code_visible()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_CODE_VISIBLE);
    }
    
    function get_course_manager_name_visible()
    {
    	return $this->get_default_property(self :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE);
    }
    
    function get_course_languages_visible()
	{
    	return $this->get_default_property(self :: PROPERTY_COURSE_LANGUAGES_VISIBLE);
    }
    
    /*
     * Setters
     */
    
    function set_course_id($course_id)
    {
    	$this->set_default_property(self :: PROPERTY_COURSE_ID, $course_id);
    } 
    
    
   	function set_feedback($feedback)
    {
    	$this->set_default_property(self :: PROPERTY_FEEDBACK, $feedback);
    } 
    
    function set_layout($layout)
    {
    	$this->set_default_property(self :: PROPERTY_LAYOUT, $layout);
    }
    
    function set_tool_shortcut($tool_shortcut)
    {
    	$this->set_default_property(self :: PROPERTY_TOOL_SHORTCUT, $tool_shortcut);
    }
    
    function set_menu($menu)
    {
    	$this->set_default_property(self :: PROPERTY_MENU, $menu);
    }
    
    function set_breadcrumb($breadcrumb)
    {
    	$this->set_default_property(self :: PROPERTY_BREADCRUMB, $breadcrumb);
    }
    
    function set_intro_text($intro_text)
    {
    	$this->set_default_property(self  :: PROPERTY_INTRO_TEXT, $intro_text);
    }
    
    function set_student_view($student_view)
    {
    	$this->set_default_property(self :: PROPERTY_STUDENT_VIEW, $student_view);
    }
    
    function set_course_code_visible($course_code_visible)
    {
    	$this->set_default_property(self :: PROPERTY_COURSE_CODE_VISIBLE, $course_code_visible);
    }
    
    function set_course_manager_name_visible($course_manager_name_visible)
    {
    	$this->set_default_property(self :: PROPERTY_COURSE_MANAGER_NAME_VISIBLE, $course_manager_name_visible);
    }
    
    function set_course_languages_visible($course_languages_visible)
    {
    	$this->set_default_property(self :: PROPERTY_COURSE_LANGUAGES_VISIBLE, $course_languages_visible);
    }
    
	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
