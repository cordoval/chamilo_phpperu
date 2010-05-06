<?php
/**
 * $Id: course.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course
 */
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_manager/weblcms_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/course_group/course_group.class.php';

/**
 *	This class represents a course in the weblcms.
 *
 *	courses have a number of default properties:
 *	- id: the numeric ID of the course object;
 *	- visual: the visual code of the course;
 *	- name: the name of the course object;
 *	- path: the course's path;
 *	- titular: the titular of this course object;
 *  - language: the language of the course object;
 *	- extlink url: the URL department;
 *	- extlink name: the name of the department;
 *	- category code: the category code of the object;
 *	- category name: the name of the category;
 *
 * To access the values of the properties, this class and its subclasses
 * should provide accessor methods. The names of the properties should be
 * defined as class constants, for standardization purposes. It is recommended
 * that the names of these constants start with the string "PROPERTY_".
 *
 */

class Course extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_COURSE_TYPE_ID = 'course_type_id';
    const PROPERTY_VISUAL = 'visual_code';
    const PROPERTY_NAME = 'title';
    const PROPERTY_TITULAR = 'titular_id';
    const PROPERTY_EXTLINK_URL = 'department_url';
    const PROPERTY_EXTLINK_NAME = 'department_name';
    const PROPERTY_CATEGORY = 'category_id';

    // Remnants from the old Chamilo system
    const PROPERTY_LAST_VISIT = 'last_visit';
    const PROPERTY_LAST_EDIT = 'last_edit';
    const PROPERTY_CREATION_DATE = 'creation_date';
    const PROPERTY_EXPIRATION_DATE = 'expiration_date';

	private $settings;

	private $layout;

	private $tools;
	
	private $rights;
	
	private $request;

	private $course_type = false;

	function Course($defaultProperties = array (), $optionalProperties = array())
	{
		parent :: __construct($defaultProperties, $optionalProperties);
		
		
	}
	
	
    /**
     * Get the default properties of all courses.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(
        		array(self :: PROPERTY_COURSE_TYPE_ID,
        			  self :: PROPERTY_VISUAL,
        			  self :: PROPERTY_CATEGORY,
        			  self :: PROPERTY_NAME,
        			  self :: PROPERTY_TITULAR,
        			  self :: PROPERTY_EXTLINK_URL,
        			  self :: PROPERTY_EXTLINK_NAME,
        			  self :: PROPERTY_CREATION_DATE,
        			  self :: PROPERTY_EXPIRATION_DATE,
        			  self :: PROPERTY_LAST_EDIT,
        			  self :: PROPERTY_LAST_VISIT));
    }
    
    
    

    /**
     * inherited
     */
    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }

    /**
     * Returns the course type id of this course object
     * @return string the course type id
     */
    function get_course_type_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_TYPE_ID);
    }

    /**
     * Returns the visual code of this course object
     * @return string the visual code
     */
    function get_visual()
    {
        return $this->get_default_property(self :: PROPERTY_VISUAL);
    }

    /**
     * Returns the category code of this course object
     * @return string the category code
     */
    function get_category()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY);
    }

    /**
     * Returns the name (Title) of this course object
     * @return string The Name
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Returns the titular of this course object
     * @return String The Titular
     */
    function get_titular()
    {
        return $this->get_default_property(self :: PROPERTY_TITULAR);
    }

    /**
     * Returns the titular as a string
     */
    function get_titular_string()
    {
        $titular_id = $this->get_titular();

        if (! is_null($titular_id))
        {
            $udm = UserDataManager :: get_instance();
            $user = $udm->retrieve_user($titular_id);
            return $user->get_lastname() . ' ' . $user->get_firstname();
        }
        else
        {
            return null;
        }
    }

    /**
     * Returns the ext url of this course object
     * @return String The URL
     */
    function get_extlink_url()
    {
        return $this->get_default_property(self :: PROPERTY_EXTLINK_URL);
    }

    /**
     * Returns the ext link name of this course object
     * @return String The Name
     */
    function get_extlink_name()
    {
        return $this->get_default_property(self :: PROPERTY_EXTLINK_NAME);
    }

    function get_creation_date()
    {
        return $this->get_default_property(self :: PROPERTY_CREATION_DATE);
    }

    function get_expiration_date()
    {
        return $this->get_default_property(self :: PROPERTY_EXPIRATION_DATE);
    }

    function get_last_edit()
    {
        return $this->get_default_property(self :: PROPERTY_LAST_EDIT);
    }

    function get_last_visit()
    {
        return $this->get_default_property(self :: PROPERTY_LAST_VISIT);
    }

    function get_settings()
    {
    	if(is_null($this->settings))
    	{
    		$settings = $this->get_data_manager()->retrieve_course_settings($this->get_id());
    		if(empty($settings))
    		{
    			$settings = new CourseSettings();
    			$settings->set_course_id($this->get_id());
    			if(!is_null($this->get_id()))
    				$settings->create();
    		}
    		$this->set_settings($settings);
    	}	
        return $this->settings;
    }
    
    function get_request()
    {
    	return $this->request;
    }

    function get_layout_settings()
    {
    	if(is_null($this->layout))
    	{
    		$layout = $this->get_data_manager()->retrieve_course_layout($this->get_id());
    	    if(empty($layout))
    		{
    			$layout = new CourseLayout();
    			$layout->set_course_id($this->get_id());
    			if(!is_null($this->get_id()))
    				$layout->create();
    		}
    		$this->set_layout_settings($layout);
    	}
        return $this->layout;
    }

    function get_tools()
    {
        if(!$this->tools)
        {
        	$wdm = WeblcmsDataManager :: get_instance();
			$this->tools = $wdm->get_course_modules($this->get_id());

			foreach ($this->tools as $index => $tool)
			{
				require_once dirname(__FILE__) . '/../tool/' . $tool->name . '/' . $tool->name . '_tool.class.php';
			}
        }
       
    	return $this->tools;
    }
    
 	function get_rights()
    {
    	if(is_null($this->rights))
    	{
    		$rights = $this->get_data_manager()->retrieve_course_rights($this->get_id());
    		if(empty($rights))
    		{
    			$rights = new CourseRights();
    			$rights->set_course_id($this->get_id());
    			if(!is_null($this->get_id()))
    				$rights->create();
    		}
    		$this->set_rights($rights);
    	}
        return $this->rights;
    }

    function get_course_type()
    {
    	if($this->course_type === false)
    	{
    		$course_type = $this->get_data_manager()->retrieve_course_type($this->get_course_type_id());
    		if(empty($course_type))
    		{
    			$course_type = NULL;
    		}
    		$this->set_course_type($course_type);
    	}
        return $this->course_type;
    }

    /**
     * Sets the course type id of this course object
     * @param int $type The course type id
     */
    function set_course_type_id($type)
    {
        $this->set_default_property(self :: PROPERTY_COURSE_TYPE_ID, $type);
    }

    /**
     * Sets the visual code of this course object
     * @param String $visual The visual code
     */
    function set_visual($visual)
    {
        $this->set_default_property(self :: PROPERTY_VISUAL, $visual);
    }

    /**
     * Sets the category code of this course object
     * @param String $visual The category code
     */
    function set_category($category)
    {
        $this->set_default_property(self :: PROPERTY_CATEGORY, $category);
    }

    /**
     * Sets the course name of this course object
     * @param String $name The name of this course object
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Sets the titular of this course object
     * @param String $titular The titular of this course object
     */
    function set_titular($titular)
    {
        $this->set_default_property(self :: PROPERTY_TITULAR, $titular);       
    }

    /**
     * Sets the extlink URL of this course object
     * @param String $url The URL if the extlink
     */
    function set_extlink_url($url)
    {
        $this->set_default_property(self :: PROPERTY_EXTLINK_URL, $url);
    }

    /**
     * Sets the extlink Name of this course object
     * @param String $name The name of the exlink
     */
    function set_extlink_name($name)
    {
        $this->set_default_property(self :: PROPERTY_EXTLINK_NAME, $name);
    }

    function set_creation_date($creation_date)
    {
        $this->set_default_property(self :: PROPERTY_CREATION_DATE, $creation_date);
    }

    function set_expiration_date($expiration_date)
    {
        $this->set_default_property(self :: PROPERTY_EXPIRATION_DATE, $expiration_date);
    }

    function set_last_edit($last_edit)
    {
        $this->set_default_property(self :: PROPERTY_LAST_EDIT, $last_edit);
    }

    function set_last_visit($last_visit)
    {
        $this->set_default_property(self :: PROPERTY_LAST_VISIT, $last_visit);
    }

    /**
     * Sets the settings of this course object
     * @param CourseSettings $settings the settings of this course object
     */
    function set_settings($settings)
    {
        $this->settings = $settings;
    }
    
    function set_request($request)
    {
    	$this->request = $request;
    }

    /**
     * Sets the layout of this course object
     * @param CourseLayout $layout the layout of this course object
     */
    function set_layout_settings($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Sets the tools of this course object
     * @param array $tools the tools of this course object
     */
    function set_tools($tools)
    {
        $this->tools = $tools;
    }
    
    /**
     * Sets the rights of this course object
     * @param array $rights the rights of this course object
     */
    function set_rights($rights)
    {
        $this->rights = $rights;
    }
    /**
     * Sets the course_type of this course object
     * @param array $course_type the course_type of this course object
     */
    function set_course_type($course_type)
    {
        $this->course_type = $course_type;
    }

    /**
     * Direct access to the setters and getters for the course settings
     * All setters include a validation to see whether or not the property is writeable
     */

    /*
     * Getters and validation whether or not the property is readable from the course's own settings
     */

    function get_language()
    {
    	if(!$this->get_language_fixed())
    	{
        	return $this->get_settings()->get_language();
    	}
        else
        	return $this->get_course_type()->get_settings()->get_language();
    }

    function get_visibility()
    {
    	if(!$this->get_visibility_fixed())
        	return $this->get_settings()->get_visibility();
        else
        	return $this->get_course_type()->get_settings()->get_visibility();
    }

	function get_access()
    {
    	if(!$this->get_access_fixed())
        	return $this->get_settings()->get_access();
        else
        	return $this->get_course_type()->get_settings()->get_access();
    }

    function get_max_number_of_members()
    {
    	if(!$this->get_max_number_of_members_fixed())
        	return $this->get_settings()->get_max_number_of_members();
        else
        	return $this->get_course_type()->get_settings()->get_max_number_of_members();
    }

    /**
     * Setters and validation to see whether they are writable
     */

    function get_titular_fixed()
    {
    	if(!is_null($this->get_course_type()))
    		return $this->get_course_type()->get_settings()->get_titular_fixed();
    	else
    		return 0;
    }
    
    function get_language_fixed()
    {
    	if(!is_null($this->get_course_type()))
    		return $this->get_course_type()->get_settings()->get_language_fixed();
    	else
    		return 0;
    }

    function set_language($language)
    {
    	if(!$this->get_language_fixed())
        	$this->get_settings()->set_language($language);
        else
        	$this->get_settings()->set_language($this->get_course_type()->get_settings()->get_language());
    }

    function get_visibility_fixed()
    {
    	if(!is_null($this->get_course_type()))
    		return $this->get_course_type()->get_settings()->get_visibility_fixed();
    	else
    		return 0;
    }

    function set_visibility($visibility)
    {
		if(!$this->get_visibility_fixed())
        	$this->get_settings()->set_visibility($visibility);
        else
        	$this->get_settings()->set_visibility($this->get_course_type()->get_settings()->get_visibility());
    }

    function get_access_fixed()
    {
    	if(!is_null($this->get_course_type()))
    		return $this->get_course_type()->get_settings()->get_access_fixed();
    	else
    		return 0;
    }

    function set_access($access)
    {
		if(!$this->get_access_fixed())
        	$this->get_settings()->set_access($access);
        else
        	$this->get_settings()->set_access($this->get_course_type()->get_settings()->get_access());
    }

    function get_max_number_of_members_fixed()
    {
    	if(!is_null($this->get_course_type()))
			return $this->get_course_type()->get_settings()->get_max_number_of_members_fixed();
		else
			return 0;
    }

    function set_max_number_of_members($max_number_of_members)
    {
		if(!$this->get_max_number_of_members_fixed())
        	$this->get_settings()->set_max_number_of_members($max_number_of_members);
        else
        	$this->get_settings()->set_max_number_of_members($this->get_course_type()->get_settings()->get_max_number_of_members());
    }

    /**
     * Direct access to the setters and getters for the course layout
     * All setters include a validation to see whether or not the property is writeable
     */

    /*
     * Getters and validation whether or not the property is readable from the course's own settings
     */
    function get_intro_text()
    {
    	if(!$this->get_intro_text_fixed())
       		return $this->get_layout_settings()->get_intro_text();
       	else
       		return $this->get_course_type()->get_layout_settings()->get_intro_text();
    }

    function get_student_view()
    {
    	if(!$this->get_student_view_fixed())
       		return $this->get_layout_settings()->get_student_view();
       	else
       		return $this->get_course_type()->get_layout_settings()->get_student_view();
    }

    function get_layout()
    {
    	if(!$this->get_layout_fixed())
       		return $this->get_layout_settings()->get_layout();
       	else
       		return $this->get_course_type()->get_layout_settings()->get_layout();
    }

    function get_tool_shortcut()
    {
    	if(!$this->get_tool_shortcut_fixed())
       		return $this->get_layout_settings()->get_tool_shortcut();
       	else
       		return $this->get_course_type()->get_layout_settings()->get_tool_shortcut();
    }

 	function get_menu()
    {
    	if(!$this->get_menu_fixed())
       		return $this->get_layout_settings()->get_menu();
       	else
       		return $this->get_course_type()->get_layout_settings()->get_menu();
    }

    function get_breadcrumb()
    {
    	if(!$this->get_breadcrumb_fixed())
       		return $this->get_layout_settings()->get_breadcrumb();
       	else
       		return $this->get_course_type()->get_layout_settings()->get_breadcrumb();
    }

    function get_feedback()
    {
    	if(!$this->get_feedback_fixed())
       		return $this->get_layout_settings()->get_feedback();
       	else
       		return $this->get_course_type()->get_layout_settings()->get_feedback();
    }

    function get_course_code_visible()
    {
    	if(!$this->get_course_code_visible_fixed())
       		return $this->get_layout_settings()->get_course_code_visible();
       	else
       		return $this->get_course_type()->get_layout_settings()->get_course_code_visible();
    }

    function get_course_manager_name_visible()
    {
    	if(!$this->get_course_manager_name_visible_fixed())
       		return $this->get_layout_settings()->get_course_manager_name_visible();
       	else
       		return $this->get_course_type()->get_layout_settings()->get_course_manager_name_visible();
    }

    function get_course_languages_visible()
	{
    	if(!$this->get_course_languages_visible_fixed())
       		return $this->get_layout_settings()->get_course_languages_visible();
       	else
       		return $this->get_course_type()->get_layout_settings()->get_course_languages_visible();
    }

    /**
     * Setters and validation to see whether they are writable
     */
    function get_feedback_fixed()
    {
    	if(!is_null($this->get_course_type()))
    		return $this->get_course_type()->get_layout_settings()->get_feedback_fixed();
    	else
    		return 0;
    }

   	function set_feedback($feedback)
    {
    	if(!$this->get_feedback_fixed())
    		$this->get_layout_settings()->set_feedback($feedback);
    	else
        	$this->get_layout_settings()->set_feedback($this->get_course_type()->get_layout_settings()->get_feedback());
    }

    function get_layout_fixed()
    {
    	if(!is_null($this->get_course_type()))
    		return $this->get_course_type()->get_layout_settings()->get_layout_fixed();
    	else
    		return 0;
    }

    function set_layout($layout)
    {
    	if(!$this->get_layout_fixed())
    		$this->get_layout_settings()->set_layout($layout);
    	else
        	$this->get_layout_settings()->set_layout($this->get_course_type()->get_layout_settings()->get_layout());
    }

    function get_tool_shortcut_fixed()
    {
    	if(!is_null($this->get_course_type()))
    		return $this->get_course_type()->get_layout_settings()->get_tool_shortcut_fixed();
    	else
    		return 0;
    }

    function set_tool_shortcut($tool_shortcut)
    {
    	if(!$this->get_tool_shortcut_fixed())
    		$this->get_layout_settings()->set_tool_shortcut($tool_shortcut);
    	else
        	$this->get_layout_settings()->set_tool_shortcut($this->get_course_type()->get_layout_settings()->get_tool_shortcut());
    }

    function get_menu_fixed()
    {
    	if(!is_null($this->get_course_type()))
    		return $this->get_course_type()->get_layout_settings()->get_menu_fixed();
    	else
    		return 0;
    }

    function set_menu($menu)
    {
    	if(!$this->get_menu_fixed())
    		$this->get_layout_settings()->set_menu($menu);
    	else
        	$this->get_layout_settings()->set_menu($this->get_course_type()->get_layout_settings()->get_menu());
    }

    function get_breadcrumb_fixed()
    {
    	if(!is_null($this->get_course_type()))
    		return $this->get_course_type()->get_layout_settings()->get_breadcrumb_fixed();
    	else
    		return 0;
    }

    function set_breadcrumb($breadcrumb)
    {
    	if(!$this->get_breadcrumb_fixed())
    		$this->get_layout_settings()->set_breadcrumb($breadcrumb);
    	else
        	$this->get_layout_settings()->set_breadcrumb($this->get_course_type()->get_layout_settings()->get_breadcrumb());
    }

    function get_intro_text_fixed()
    {
    	if(!is_null($this->get_course_type()))
    		return $this->get_course_type()->get_layout_settings()->get_intro_text_fixed();
    	else
    		return 0;
    }

    function set_intro_text($intro_text)
    {
    	if(!$this->get_intro_text_fixed())
    		$this->get_layout_settings()->set_intro_text($intro_text);
    	else
        	$this->get_layout_settings()->set_intro_text($this->get_course_type()->get_layout_settings()->get_intro_text());
    }

    function get_student_view_fixed()
    {
    	if(!is_null($this->get_course_type()))
    		return $this->get_course_type()->get_layout_settings()->get_student_view_fixed();
    	else
    		return 0;
    }

    function set_student_view($student_view)
    {
    	if(!$this->get_student_view_fixed())
    		$this->get_layout_settings()->set_student_view($student_view);
    	else
        	$this->get_layout_settings()->set_student_view($this->get_course_type()->get_layout_settings()->get_student_view());
    }

    function get_course_code_visible_fixed()
    {
    	if(!is_null($this->get_course_type()))
    		return $this->get_course_type()->get_layout_settings()->get_course_code_visible_fixed();
    	else
    		return 0;
    }

    function set_course_code_visible($course_code_visible)
    {
    	if(!$this->get_course_code_visible_fixed())
    		$this->get_layout_settings()->set_course_code_visible($course_code_visible);
    	else
        	$this->get_layout_settings()->set_course_code_visible($this->get_course_type()->get_layout_settings()->get_course_code_visible());
    }

    function get_course_manager_name_visible_fixed()
    {
    	if(!is_null($this->get_course_type()))
    		return $this->get_course_type()->get_layout_settings()->get_course_manager_name_visible_fixed();
    	else
    		return 0;
    }

    function set_course_manager_name_visible($course_manager_name_visible)
    {
    	if(!$this->get_course_manager_name_visible_fixed())
    		$this->get_layout_settings()->set_course_manager_name_visible($course_manager_name_visible);
    	else
        	$this->get_layout_settings()->set_course_manager_name_visible($this->get_course_type()->get_layout_settings()->get_course_manager_name_visible());
    }

    function get_course_languages_visible_fixed()
    {
    	if(!is_null($this->get_course_type()))
    		return $this->get_course_type()->get_layout_settings()->get_course_languages_visible_fixed();
    	else
    		return 0;
    }

    function set_course_languages_visible($course_languages_visible)
    {
    	if(!$this->get_course_languages_visible_fixed())
    		$this->get_layout_settings()->set_course_languages_visible($course_languages_visible);
    	else
        	$this->get_layout_settings()->set_course_languages_visible($this->get_course_type()->get_layout_settings()->get_course_languages_visible());
    }

	/**
     * Direct access to the setters and getters for the rights settings
     * All setters include a validation to see whether or not the property is writeable
     */

    /*
     * Getters and validation whether or not the property is readable from the course's own settings
     */

    function can_user_subscribe($user)
    {
    	$max_members = $this->get_max_number_of_members();
    	if($max_members != 0)
    	{
    		$subscribed_users = $this->has_subscribed_users();
    		if($subscribed_users >= $max_members)
    		{
    			return CourseGroupSubscribeRight :: SUBSCRIBE_NONE;	
    		}   		
    	}
    	$current_right = $this->can_group_subscribe(0);
        $group_ids = $user->get_groups(true);
        foreach($group_ids as $group_id)
        {
        	$right = $this->can_group_subscribe($group_id);
				
        	if($right > $current_right)
        		$current_right = $right;      		    		
        }        
    	return $current_right;
    }
    
	function can_user_unsubscribe($user)
    {
    	//TODO : remove 0 !!
    	$current_right = $this->can_group_unsubscribe(0);
        $group_ids = $user->get_groups(true);
        foreach($group_ids as $group_id)
        {
        	$right = $this->can_group_unsubscribe($group_id);
				
        	if($right > $current_right)
        		$current_right = $right;      		    		
        }        
        return $current_right;
    }
    
    function can_group_subscribe($group_id)
    {
    	$right = $this->get_rights()->can_group_subscribe($group_id);
    	switch($right)
    	{
    		case CourseGroupSubscribeRight :: SUBSCRIBE_DIRECT :
    			if(!$this->get_direct_subscribe_available())
    				return CourseGroupSubscribeRight :: SUBSCRIBE_NONE;
    			break;
    		case CourseGroupSubscribeRight :: SUBSCRIBE_REQUEST :
    			if(!$this->get_request_subscribe_available())
    				return CourseGroupSubscribeRight :: SUBSCRIBE_NONE;
    			break;
    		case CourseGroupSubscribeRight :: SUBSCRIBE_CODE :
    			if(!$this->get_code_subscribe_available())
    				return CourseGroupSubscribeRight :: SUBSCRIBE_NONE;
    			break;
    		default : return CourseGroupSubscribeRight :: SUBSCRIBE_NONE;
    	}
    	return $right;
    }
    
    
    function can_group_unsubscribe($group_id)
    {
    	if($this->get_unsubscribe_available())
    		return $this->get_rights()->can_group_unsubscribe($group_id);
    	else
    		return 0;
    }

    function get_code()
    {
    	return $this->get_rights()->get_code();
    }

    function get_direct_subscribe_available()
    {
    	if(!$this->get_direct_subscribe_fixed())
        	return $this->get_rights()->get_direct_subscribe_available();
        else
        	return $this->get_course_type()->get_rights()->get_direct_subscribe_available();
    }

    function get_request_subscribe_available()
    {
    	if(!$this->get_request_subscribe_fixed())
        	return $this->get_rights()->get_request_subscribe_available();
        else
        	return $this->get_course_type()->get_rights()->get_request_subscribe_available();
    }

    function get_code_subscribe_available()
    {
    	if(!$this->get_code_subscribe_fixed())
        	return $this->get_rights()->get_code_subscribe_available();
        else
        	return $this->get_course_type()->get_rights()->get_code_subscribe_available();
    }

    function get_unsubscribe_available()
    {
    	if(!$this->get_unsubscribe_fixed())
        	return $this->get_rights()->get_unsubscribe_available();
        else
        	return $this->get_course_type()->get_rights()->get_unsubscribe_available();
    }
    
    /**
     * Setters and validation to see whether they are writable
     */
    
    function set_code($code)
    {
    	if($this->get_code_subscribe_available())
    		$this->get_rights()->set_code($code);
    	else
    		$this->get_rights()->set_code(null);
    }

    function get_direct_subscribe_fixed()
    {
    	if(!is_null($this->get_course_type()))
    		return $this->get_course_type()->get_rights()->get_direct_subscribe_fixed();
    	else
    		return 0;
    }

    function set_direct_subscribe_available($direct)
    {
    	if(!$this->get_direct_subscribe_fixed())
        	$this->get_rights()->set_direct_subscribe_available($direct);
        else
        	$this->get_rights()->set_direct_subscribe_available($this->get_course_type()->get_rights()->get_direct_subscribe_available());
    }

    function get_request_subscribe_fixed()
    {
    	if(!is_null($this->get_course_type()))
    		return $this->get_course_type()->get_rights()->get_request_subscribe_fixed();
    	else
    		return 0;
    }

    function set_request_subscribe_available($request)
    {
    	if(!$this->get_request_subscribe_fixed())
        	$this->get_rights()->set_request_subscribe_available($request);
        else
        	$this->get_rights()->set_request_subscribe_available($this->get_course_type()->get_rights()->get_request_subscribe_available());
    }

    function get_code_subscribe_fixed()
    {
    	if(!is_null($this->get_course_type()))
    		return $this->get_course_type()->get_rights()->get_code_subscribe_fixed();
    	else
    		return 0;
    }

    function set_code_subscribe_available($code)
    {
    	if(!$this->get_code_subscribe_fixed())
        	$this->get_rights()->set_code_subscribe_available($code);
        else
        	$this->get_rights()->set_code_subscribe_available($this->get_course_type()->get_rights()->get_code_subscribe_available());
    }
    
    function get_unsubscribe_fixed()
    {
    	if(!is_null($this->get_course_type()))
    		return $this->get_course_type()->get_rights()->get_unsubscribe_fixed();
    	else
    		return 0;
    }

    function set_unsubscribe_available($code)
    {
    	if(!$this->get_unsubscribe_fixed())
        	$this->get_rights()->set_unsubscribe_available($code);
        else
        	$this->get_rights()->set_unsubscribe_available($this->get_course_type()->get_rights()->get_unsubscribe_available());
    }
    
    /**
     * Creates the course object in persistent storage
     * @return boolean
     */
    function create()
    {
        $wdm = WeblcmsDataManager :: get_instance();

        if (! $wdm->create_course($this))
            return false;
		
        $settings = $this->get_settings();
        $settings->set_course_id($this->get_id());
        if (! $settings->create())
			return false;

		$layout = $this->get_layout_settings();
		$layout->set_course_id($this->get_id());
		if(! $layout->create())
			return false;

		$rights = $this->get_rights();
		$rights->set_course_id($this->get_id());
		if(! $rights->create())
			return false;
		
        if (! $this->initialize_course_sections())
            return false;
			
		if(!$this->tools)
		{
			$course_type_id = $this->get_course_type_id();
			if(!empty($course_type_id))
				$this->tools = CourseModule :: convert_tools($this->get_course_type()->get_tools(), $this->get_id(), true);
			else
				$this->tools = CourseModule :: convert_tools(WeblcmsDataManager :: get_tools('basic'), $this->get_id());
		}
		else
		{
			foreach($this->tools as $tool)
				$tool->set_course_code($this->get_id());
		}
		
		if(!$wdm->create_course_modules($this->tools, $this->get_id()))
			return false;
			
        require_once (dirname(__FILE__) . '/../category_manager/content_object_publication_category.class.php');
        $dropbox = new ContentObjectPublicationCategory();
        $dropbox->create_dropbox($this->get_id());

        $location = new Location();
        $location->set_location($this->get_name());
        $location->set_application(WeblcmsManager :: APPLICATION_NAME);
        $location->set_type_from_object($this);
        $location->set_identifier($this->get_id());

        $parent = WeblcmsRights :: get_location_id_by_identifier('course_category', 1);
        //echo 'parent : ' . $parent;


        if ($parent)
        {
            $location->set_parent($parent);
        }
        else
        {
            $location->set_parent(0);
        }

        if (! $location->create())
        {
            return false;
        }
        
    	if(!$this->create_root_course_group())
        {
        	return false;
        }

        return true;
    }

    function create_all()
    {
        $wdm = WeblcmsDataManager :: get_instance();
        return $wdm->create_course_all($this);
    }

 	function delete()
    {
        $dm = $this->get_data_manager();
        return $dm->delete_course($this->get_id());
    }

    /**
     * Checks whether the given user is a course admin in this course
     * @param int $user_id
     * @return boolean
     */
    function is_course_admin($user)
    {
        if ($user->is_platform_admin())
        {
            return true;
        }
        $wdm = WeblcmsDataManager :: get_instance();
        return $wdm->is_course_admin($this, $user->get_id());
    }

    /**
     * Determines if this course has a theme
     * @return boolean
     */
    function has_theme()
    {
        return (! is_null($this->get_layout()->get_theme()) ? true : false);
    }

    function has_subscribed_users()
    {
        $relation_condition = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $this->get_id());
        return $this->get_data_manager()->count_course_user_relations($relation_condition);
    }

    /**
     * Gets the subscribed users of this course
     * @return array An array of CourseUserRelation objects
     */
    function get_subscribed_users()
    {
        $relation_condition = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $this->get_id());
        return $this->get_data_manager()->retrieve_course_user_relations($relation_condition)->as_array();
    }

    function has_subscribed_groups()
    {
        $relation_condition = new EqualityCondition(CourseGroupRelation :: PROPERTY_COURSE_ID, $this->get_id());
        return $this->get_data_manager()->count_course_group_relations($relation_condition);
    }

    /**
     * Gets the subscribed groups of this course
     * @return array An array of CourseGroupRelation objects
     */
    function get_subscribed_groups()
    {
        $relation_condition = new EqualityCondition(CourseGroupRelation :: PROPERTY_COURSE_ID, $this->get_id());
        return $this->get_data_manager()->retrieve_course_group_relations($relation_condition)->as_array();
    }

    /**
     * Gets the course_groups defined in this course
     * @return array An array of CourseGroup objects
     */
    function get_course_groups($as_array = true)
    {
        $wdm = WeblcmsDataManager :: get_instance();
        $condition = new EqualityCondition(CourseGroup :: PROPERTY_COURSE_CODE, $this->get_id());
        $result = $wdm->retrieve_course_groups($condition, null, null, array(new ObjectTableOrder(CourseGroup :: PROPERTY_NAME)));
        return ($as_array ? $result->as_array() : $result);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    function initialize_course_sections()
    {
        $sections = array();
        $sections[] = array('name' => Translation :: get('Tools'), 'type' => 1, 'order' => 1);
        $sections[] = array('name' => Translation :: get('Links'), 'type' => 2, 'order' => 2);
        $sections[] = array('name' => Translation :: get('Disabled'), 'type' => 0, 'order' => 3);
        $sections[] = array('name' => Translation :: get('CourseAdministration'), 'type' => 3, 'order' => 4);

        foreach ($sections as $section)
        {
            $course_section = new CourseSection();
            $course_section->set_course_code($this->get_id());
            $course_section->set_name($section['name']);
            $course_section->set_type($section['type']);
            $course_section->set_visible(true);
            if (! $course_section->create())
            {
                return false;
            }
        }

        return true;
    }

    function create_root_course_group()
    {
    	$group = new CourseGroup();
    	$group->set_course_code($this->get_id());
    	$group->set_name($this->get_name());
    	return $group->create();
    }
}
?>