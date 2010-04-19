<?php
/**
 * $Id: course_rights.class.php 216 2009-11-13 14:08:06Z Tristan $
 * @package application.lib.weblcms.course
 */
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_manager/weblcms_manager.class.php';

class CourseRights extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_COURSE_ID = 'course_id';
    const PROPERTY_DIRECT_SUBSCRIBE_AVAILABLE = 'direct_subscribe_available';
    const PROPERTY_REQUEST_SUBSCRIBE_AVAILABLE = 'request_subscribe_available';
    const PROPERTY_CODE_SUBSCRIBE_AVAILABLE = 'code_subscribe_available';
    const PROPERTY_UNSUBSCRIBE_AVAILABLE = 'unsubscribe_available';
    const PROPERTY_CODE = 'code';

    private $group_subscribe_rights = array();
    private $goup_unsubscribe_rights = array();
    
    /**
     * Get the default properties of all courses.
     * @return array The property names.
     */
    static function get_default_property_names($extended_property_names = array())
    {
        if(empty($extended_property_names)) $extended_property_names = array(self :: PROPERTY_COURSE_ID, self :: PROPERTY_CODE);
        return array_merge($extended_property_names,
        		array(self :: PROPERTY_DIRECT_SUBSCRIBE_AVAILABLE,
        			  self :: PROPERTY_REQUEST_SUBSCRIBE_AVAILABLE,
        			  self :: PROPERTY_CODE_SUBSCRIBE_AVAILABLE,
        			  self :: PROPERTY_UNSUBSCRIBE_AVAILABLE));
    }
    
    /**
     * inherited
     */
    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }

    function get_course_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_ID);
    }

    function get_direct_subscribe_available()
    {
        return $this->get_default_property(self :: PROPERTY_DIRECT_SUBSCRIBE_AVAILABLE);
    }

    function get_request_subscribe_available()
    {
        return $this->get_default_property(self :: PROPERTY_REQUEST_SUBSCRIBE_AVAILABLE);
    } 
    
    function get_code_subscribe_available()
    {
        return $this->get_default_property(self :: PROPERTY_CODE_SUBSCRIBE_AVAILABLE);
    }
    
    function get_unsubscribe_available()
    {
        return $this->get_default_property(self :: PROPERTY_UNSUBSCRIBE_AVAILABLE);
    } 
    
    function get_code()
    {
        return $this->get_default_property(self :: PROPERTY_CODE);
    }

    function set_course_id($course_id)
    {
        return $this->set_default_property(self :: PROPERTY_COURSE_ID, $course_id);
    }

    function set_direct_subscribe_available($direct_subscribe_available)
    {
        return $this->set_default_property(self :: PROPERTY_DIRECT_SUBSCRIBE_AVAILABLE, $direct_subscribe_available);
    }

    function set_request_subscribe_available($request_subscribe_available)
    {
        return $this->set_default_property(self :: PROPERTY_REQUEST_SUBSCRIBE_AVAILABLE, $request_subscribe_available);
    } 
    
    function set_code_subscribe_available($code_subscribe_available)
    {
        return $this->set_default_property(self :: PROPERTY_CODE_SUBSCRIBE_AVAILABLE, $code_subscribe_available);
    } 
    
    function set_unsubscribe_available($unsubscribe_available)
    {
        return $this->set_default_property(self :: PROPERTY_UNSUBSCRIBE_AVAILABLE, $unsubscribe_available);
    } 
    
    function set_code($code)
    {
        return $this->set_default_property(self :: PROPERTY_CODE, $code);
    } 
    
    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
    
    //Subscribe/Unsubscribe getters and setters
	function can_group_subscribe($group_id)
	{
		//If none of the subscribes are available return SUBSCRIBE_NONE
		if($this->get_direct_subscribe_available() || $this->get_request_subscribe_available() || $this->get_code_subscribe_available())
		{
			//Check if the group right has already been retrieved from the database.
			if(isset($group_subscribe_rights[$group_id]))
			{
				//if the value is numeric it means that the right is set in the parent of the group
				//so return the parent's right else the group's right
				if(is_numeric($group_subscribe_rights[$group_id]))
					return $this->can_group_subscribe($group_subscribe_rights[$group_id]);
				else
					return $group_subscribe_rights[$group_id]->get_subscribe();
			}
			//else retrieve group from the database
			else
			{
				$right = WeblcmsDatamanager::get_instance()->retrieve_course_group_subscribe_right($this->get_course_id(), $group_id);
				//check the result returned from the database
				//there was a result from the database
				if(!empty($right))
				{
					//check whether or not the right is available before returning it
					//if not set the right to none
					switch($right->get_subscribe())
					{
						case CourseGroupSubscribeRight :: SUBSCRIBE_DIRECT:
							if(!$this->get_direct_subscribe_available())
								$right->set_subscribe(CourseGroupSubscribeRight :: SUBSCRIBE_NONE);
							break;
						case CourseGroupSubscribeRight :: SUBSCRIBE_REQUEST:
							if(!$this->get_request_subscribe_available())
								$right->set_subscribe(CourseGroupSubscribeRight :: SUBSCRIBE_NONE);
							break;
						case CourseGroupSubscribeRight :: SUBSCRIBE_CODE:
							if(!$this->get_code_subscribe_available())
								$right->set_subscribe(CourseGroupSubscribeRight :: SUBSCRIBE_NONE);
							break;
					}
					//register the right in the rightsarray and return.
					$group_subscribe_rights[$group_id] = $right;
					return $right->get_subscribe();
				}
				//no result
				else
				{
					//retrieve the groups information and check if it has a parent, if so check whether or not the parent can subscribe.
					$group = GroupDataManager :: get_instance()->retrieve_group($group_id);
					if(!empty($group))
					{
						$group_subscribe_rights[$group_id] = $group->get_parent();
						return $this->can_group_subscribe($group->get_parent());
					}
					//if not, register group in the rightsarray with no right and return the right.
					else
					{
						$right = new CourseGroupSubscribeRight();
						$right->set_subscribe(CourseGroupSubscribeRight :: SUBSCRIBE_NONE);
						$group_subscribe_rights[$group_id] = $right;
						return CourseGroupSubscribeRight :: SUBSCRIBE_NONE;
					}
				}
			}
		}
		else return CourseGroupSubscribeRight :: SUBSCRIBE_NONE;
	}
	
	function can_group_unsubscribe($group_id)
	{
		if($this->get_unsubscribe_available())
		{
			if(isset($group_unsubscribe_rights[$group_id]))
				return $group_unsubscribe_rights[$group_id]->get_unsubscribe();
			else
			{
				$right = WeblcmsDatamanager::get_instance()->retrieve_course_group_unsubscribe_right($this->get_course_id(), $group_id);
				if(empty($right))
				{
					$right = new CourseGroupUnsubscribeRight();
					$right->set_unsubscribe(0);
				}
				$group_unsubscribe_rights[$group_id] = $right;
				return $right->get_unsubscribe();
			}
		}
		else return 0;
	}
}
?>