<?php
/**
 * $Id: course_type.class.php 216 2010-02-25 11:06:00Z Yannick & Tristan$
 * @package application.lib.weblcms.course_type
 */
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_manager/weblcms_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/course/course_settings.class.php';

class CourseType extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_ACTIVE = 'active';
	
    private $settings;
	
    private $layout;
    
    private $tools;
    
    private $rights;
    
    /**
     * Get the default properties of all courses.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(
        	array(self :: PROPERTY_NAME,
        		  self :: PROPERTY_ACTIVE,
        		  self :: PROPERTY_DESCRIPTION));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }

    /**
     * Returns the name of this coursetype object
     * @return String the name
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }
    
    function get_active()
    {
    	return $this->get_default_property(self :: PROPERTY_ACTIVE);
    }

    /**
     * Returns the description of this coursetype object
     * @return String the description
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }
    
	/**
     * Returns the settings of this coursetype object
     * @return CourseTypeSettings the settings
     */
    function get_settings()
    {
    	if(is_null($this->settings))
    		$this->set_settings($this->get_data_manager()->retrieve_course_type_settings($this->get_id()));
        return $this->settings;
    }
	
	/**
     * Returns the layout of this coursetype object
     * @return CourseTypeLayout the layout
     */
    function get_layout_settings()
    {
    	if(is_null($this->layout))
    		$this->set_layout($this->get_data_manager()->retrieve_course_type_layout($this->get_id()));
        return $this->layout;
    }
    
	/**
     * Returns the rights of this coursetype object
     * @return CourseTypeRights the layout
     */
    function get_rights()
    {
    	if(is_null($this->rights))
    		$this->set_rights($this->get_data_manager()->retrieve_course_type_rights($this->get_id()));
        return $this->rights;
    }
    
	/**
     * Returns the tools of this coursetype object
     * @return array the tools
     */
    function get_tools()
    {
        return $this->tools;
    }
    
    /**
     * Sets the coursetype name of this coursetype object
     * @param String $name The name of this coursetype object
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }
    
    function set_active($active)
    {
    	$this->set_default_property(self :: PROPERTY_ACTIVE, $active);
    }

    /**
     * Sets the description of this coursetype object
     * @param String $description the description of this coursetype object
     */
    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }
    
    /**
     * Sets the settings of this coursetype object
     * @param CourseTypeSettings $settings the settings of this coursetype object
     */
    function set_settings($settings)
    {
        $this->settings = $settings;
    }
    
    /**
     * Sets the layout of this coursetype object
     * @param CourseTypeLayout $layout the layout of this coursetype object
     */
    function set_layout_settings($layout)
    {
        $this->layout = $layout;
    }
    
    /**
     * Sets the rights of this coursetype object
     * @param CourseTypeRights $rights the rights of this coursetype object
     */
    function set_rights($rights)
    {
        $this->rights = $rights;
    }
    
    /**
     * Sets the tools of this coursetype object
     * @param array $tools the tools of this coursetype object
     */
    function set_tools($tools)
    {
        $this->tools = $tools;
    }

    
    /**
     * Retrieves the creation right of the user
     */
    function can_user_create($user)
    {
    	$current_right = $this->get_rights()->can_group_create(0);
        $group_ids = $user->get_groups(true);
        foreach($group_ids as $group_id)
        {
        	$right = $this->get_rights()->can_group_create($group_id);
				
        	if($right > $current_right)
        		$current_right = $right;      		    		
        }        
        return $current_right;
    }
    
    /**
     * Creates the course type object in persistent storage
     * @return boolean
     */
    function create()
    {
        $wdm = WeblcmsDataManager :: get_instance();
        if (! $wdm->create_course_type($this))
        {
            return false;
        }

//        require_once (dirname(__FILE__) . '/../category_manager/content_object_publication_category.class.php');
//        $dropbox = new ContentObjectPublicationCategory();
//        $dropbox->create_dropbox($this->get_id());
//
//        $location = new Location();
//        $location->set_location($this->get_name());
//        $location->set_application(WeblcmsManager :: APPLICATION_NAME);
//        $location->set_type_from_object($this);
//        $location->set_identifier($this->get_id());
//
//        $parent = WeblcmsRights :: get_location_id_by_identifier('course_category', 1);
//        //echo 'parent : ' . $parent;
//
//
//        if ($parent)
//        {
//            $location->set_parent($parent);
//        }
//        else
//        {
//            $location->set_parent(0);
//        }
//
//        if (! $location->create())
//        {
//            return false;
//        }

        return true;
    }
//
//    function create_course_type_all()
//    {
//        $wdm = WeblcmsDataManager :: get_instance();
//        return $wdm->create_course_type_all($this);
//    }
//    
    
	static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
    
   /**
     * Checks whether the given user is a course type admin
     * @param User $user
     * @return boolean
     */
    function is_course_type_admin($user)
    {
        if ($user->is_platform_admin())
        {
            return true;
        }
        $wdm = WeblcmsDataManager :: get_instance();
        return $wdm->is_course_type_admin($this, $user->get_id());
    }
}
?>