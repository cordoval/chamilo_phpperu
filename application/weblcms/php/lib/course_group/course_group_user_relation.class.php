<?php
/**
 * $Id: course_group_user_relation.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course_group
 */
require_once dirname(__FILE__) . '/../weblcms_data_manager.class.php';

class CourseGroupUserRelation
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_COURSE_GROUP = 'course_group_id';
    const PROPERTY_USER = 'user_id';
    
    private $defaultProperties;

    /**
     * Creates a new course user relation object.
     * @param int $id The numeric ID of the course user relation object. May be omitted
     *                if creating a new object.
     * @param array $defaultProperties The default properties of the course user relation
     *                object. Associative array.
     */
    function CourseGroupUserRelation($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property of this course user relation object by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties of this course user relation object.
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Sets a default property of this course user relation object by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Get the default properties of all course user relations.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_COURSE_GROUP, self :: PROPERTY_USER);
    }

    /**
     * Returns the course group of this course group user relation object
     * @return int
     */
    function get_course_group()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_GROUP);
    }

    /**
     * Sets the course group of this course group user relation object
     * @param int $course
     */
    function set_course_group($course_group)
    {
        $this->set_default_property(self :: PROPERTY_COURSE_GROUP, $course_group);
    }

    /**
     * Returns the user of this course user relation object
     * @return int
     */
    function get_user()
    {
        return $this->get_default_property(self :: PROPERTY_USER);
    }

    /**
     * Sets the user of this course user relation object
     * @param int $user
     */
    function set_user($user)
    {
        $this->set_default_property(self :: PROPERTY_USER, $user);
    }

    /**
     * Gets the user
     * @return User
     * @todo The functions get_user and set_user should work with a User object
     * and not with the user id's!
     */
    function get_user_object()
    {
        $udm = UserDataManager :: get_instance();
        return $udm->retrieve_user($this->get_user());
    }

    /**
     * Creates the course user relation object in persistent storage
     * @return boolean
     */
    function create()
    {
        $wdm = WeblcmsDataManager :: get_instance();
        $success = $wdm->create_course_group_user_relation($this);
        
        if (! $success)
        {
            return false;
        }
        return true;
    }

    /**
     * Deletes the course user relation object from persistent storage
     * @return boolean
     */
    function delete()
    {
        $wdm = WeblcmsDataManager :: get_instance();
        $success = $wdm->delete_course_user_group_relation($this);
        if (! $success)
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
?>