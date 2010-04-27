<?php
/**
 * $Id: course_rights.class.php 216 2009-11-13 14:08:06Z Tristan $
 * @package application.lib.weblcms.course
 */
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_manager/weblcms_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/course/course_rights.class.php';

class CourseTypeRights extends CourseRights
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_COURSE_TYPE_ID = 'course_type_id';
    const PROPERTY_DIRECT_SUBSCRIBE_FIXED = 'direct_subscribe_fixed';
    const PROPERTY_REQUEST_SUBSCRIBE_FIXED = 'request_subscribe_fixed';
    const PROPERTY_CODE_SUBSCRIBE_FIXED = 'code_subscribe_fixed';
    const PROPERTY_UNSUBSCRIBE_FIXED = 'unsubscribe_fixed';
    
    const PROPERTY_CREATION_AVAILABLE = 'creation_available';
    const PROPERTY_CREATION_ON_REQUEST_AVAILABLE = 'creation_on_request_available';

    private $group_creation_rights = array();
    
    /**
     * Get the default properties of all courses.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
    	return parent :: get_default_property_names(
        		array(self :: PROPERTY_COURSE_TYPE_ID,
        			  self :: PROPERTY_DIRECT_SUBSCRIBE_FIXED,
        			  self :: PROPERTY_REQUEST_SUBSCRIBE_FIXED,
        			  self :: PROPERTY_CODE_SUBSCRIBE_FIXED,
        			  self :: PROPERTY_UNSUBSCRIBE_FIXED,
        			  self :: PROPERTY_CREATION_AVAILABLE,
        			  self :: PROPERTY_CREATION_ON_REQUEST_AVAILABLE));
    }
    
    /**
     * inherited
     */
    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }

    function get_course_type_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_TYPE_ID);
    }

    function get_direct_subscribe_fixed()
    {
        return $this->get_default_property(self :: PROPERTY_DIRECT_SUBSCRIBE_FIXED);
    }

    function get_request_subscribe_fixed()
    {
        return $this->get_default_property(self :: PROPERTY_REQUEST_SUBSCRIBE_FIXED);
    } 
    
    function get_code_subscribe_fixed()
    {
        return $this->get_default_property(self :: PROPERTY_CODE_SUBSCRIBE_FIXED);
    }
    
    function get_unsubscribe_fixed()
    {
        return $this->get_default_property(self :: PROPERTY_UNSUBSCRIBE_FIXED);
    } 
    
    function get_creation_available()
    {
        return $this->get_default_property(self :: PROPERTY_CREATION_AVAILABLE);
    }
    
    function get_creation_on_request_available()
    {
        return $this->get_default_property(self :: PROPERTY_CREATION_ON_REQUEST_AVAILABLE);
    } 
    
    function set_course_type_id($course_type_id)
    {
        return $this->set_default_property(self :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
    }

    function set_direct_subscribe_fixed($direct_subscribe_fixed)
    {
        return $this->set_default_property(self :: PROPERTY_DIRECT_SUBSCRIBE_FIXED, $direct_subscribe_fixed);
    }

    function set_request_subscribe_fixed($request_subscribe_fixed)
    {
        return $this->set_default_property(self :: PROPERTY_REQUEST_SUBSCRIBE_FIXED, $request_subscribe_fixed);
    } 
    
    function set_code_subscribe_fixed($code_subscribe_fixed)
    {
        return $this->set_default_property(self :: PROPERTY_CODE_SUBSCRIBE_FIXED, $code_subscribe_fixed);
    } 
    
    function set_unsubscribe_fixed($unsubscribe_fixed)
    {
        return $this->set_default_property(self :: PROPERTY_UNSUBSCRIBE_FIXED, $unsubscribe_fixed);
    } 
    
     function set_creation_available($creation_available)
    {
        return $this->set_default_property(self :: PROPERTY_CREATION_AVAILABLE, $creation_available);
    } 
    
    function set_creation_on_request_available($creation_on_request_available)
    {
        return $this->set_default_property(self :: PROPERTY_CREATION_ON_REQUEST_AVAILABLE, $creation_on_request_available);
    }
    
    //creation
	function can_group_create($group_id)
	{
		//If none of the rights are available return CREATE_NONE
		if($this->get_creation_available() || $this->get_creation_on_request_available())
		{
			//Check if the group right has already been retrieved from the database.
			if(isset($this->group_creation_rights[$group_id]))
			{
				//if the value is numeric it means that the right is set in the parent of the group
				//so return the parent's right else the group's right
				if(is_numeric($this->group_creation_rights[$group_id]))
					return $this->can_group_create($this->group_creation_rights[$group_id]);
				else
					return $this->group_creation_rights[$group_id]->get_create();
			}
			//else retrieve group from the database
			else
			{
				$right = WeblcmsDatamanager::get_instance()->retrieve_course_type_group_creation_right($this->get_course_type_id(), $group_id);
				//check the result returned from the database
				//there was a result from the database
				if(!empty($right))
				{
					//check whether or not the right is available before returning it
					//if not set the right to none
					switch($right->get_create())
					{
						case CourseTypeGroupCreationRight :: CREATE_DIRECT:
							if(!$this->get_creation_available())
								$right->set_create(CourseTypeGroupCreationRight :: CREATE_NONE);
							break;
						case CourseTypeGroupCreationRight :: CREATE_REQUEST:
							if(!$this->get_creation_on_request_available())
								$right->set_create(CourseTypeGroupCreationRight :: CREATE_NONE);
							break;
					}
					//register the right in the rightsarray and return.
					$this->group_creation_rights[$group_id] = $right;
					return $right->get_create();
				}
				//no result
				else
				{
					//retrieve the groups information and check if it has a parent, if so check whether or not the parent can creation.
					$group = GroupDataManager :: get_instance()->retrieve_group($group_id);
					if(!empty($group))
					{
						$this->group_creation_rights[$group_id] = $group->get_parent();
						return $this->can_group_create($group->get_parent());
					}
					else
					{
						$right = new CourseTypeGroupCreationRight();
						$validation = false;
						//check for the everybody right
						$condition_course_type_id = new EqualityCondition(CourseTypeGroupCreationRight :: PROPERTY_COURSE_TYPE_ID, $this->get_course_type_id());
						$condition_right = new EqualityCondition(CourseTypeGroupCreationRight :: PROPERTY_CREATE, CourseTypeGroupCreationRight :: CREATE_DIRECT);
						$condition = new AndCondition(array($condition_course_type_id, $condition_right));	
						$count =  WeblcmsDatamanager::get_instance()->count_course_type_group_creation_rights($condition);
						if($count == 0 && $this->get_direct_creation_available())
						{
							$right->set_create(CourseTypeGroupCreationRight :: CREATE_DIRECT);
							$validation = true;
						}

						$condition_right = new EqualityCondition(CourseTypeGroupCreationRight :: PROPERTY_CREATE, CourseTypeGroupCreationRight :: CREATE_REQUEST);
						$condition = new AndCondition(array($condition_course_type_id, $condition_right));	
						$count =  WeblcmsDatamanager::get_instance()->count_course_type_group_creation_rights($condition);
						if($count == 0 && $this->get_request_creation_available() && !$validation)
						{
							$right->set_create(CourseTypeGroupCreationRight :: CREATE_REQUEST);
							$validation = true;
						}
						//if not, register group in the rightsarray with no right and return the right.
						if(!$validation)
							$right->set_create(CourseTypeGroupCreationRight :: CREATE_NONE);
						
						$this->group_creation_rights[$group_id] = $right;
						return $right->get_create();
					}

				}
			}
		}
		else return CourseGroupCreationRight :: CREATE_NONE;
	}
 
    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

}
?>