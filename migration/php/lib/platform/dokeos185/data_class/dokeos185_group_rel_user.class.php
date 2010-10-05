<?php

/**
 * $Id: dokeos185_group_rel_user.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class represents an old Dokeos 1.8.5 Group Tutor Relation
 *
 * @author David Van Wayenbergh
 */

class Dokeos185GroupRelUser extends Dokeos185CourseDataMigrationDataClass
{

    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'group_rel_user';
    /**
     * group user relation properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_GROUP_ID = 'group_id';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_ROLE = 'role';
    
    /**
     * Default properties of the group tutor relation object, stored in an associative
     * array.
     */
    private $defaultProperties;

    /**
     * Creates a new group tutor relation object.
     * @param array $defaultProperties The default properties of the group tutor relation
     *                                 object. Associative array.
     */
    function Dokeos185GroupRelUser($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property of this group tutor relation object by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties of this group tutor relation.
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the default properties of all link categories.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_GROUP_ID, self :: PROPERTY_STATUS, self :: PROPERTY_ROLE);
    }

    /**
     * Sets a default property of this group tutor relation by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Sets the default properties of this link.
     * @param array $defaultProperties An associative array containing the properties.
     */
    function set_default_properties($defaultProperties)
    {
        return $this->defaultProperties = $defaultProperties;
    }

    /**
     * Returns the id of this group tutor relation.
     * @return int The id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the user_id of this group tutor relation.
     * @return String The user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Returns the group_id of this group tutor relation.
     * @return String The group_id.
     */
    function get_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_GROUP_ID);
    }

    /**
     * Returns the status of this group tutor relation.
     * @return String The status.
     */
    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    /**
     * Returns the role of this group tutor relation.
     * @return String The role.
     */
    function get_role()
    {
        return $this->get_default_property(self :: PROPERTY_ROLE);
    }

    /**
     * Check if the group user relation is valid
     * @param Course $course the course
     * @return true if the group user relation is valid 
     */
    function is_valid()
    {
        if (! $this->get_user_id() || ! $this->get_group_id())
        {
            $this->create_failed_element($this->get_id());
            return false;
        }

        return true;
    }

    /**
     * Convert to new group user relation 
     * @param Course $course the course
     * @return the new group user relation
     */
    function convert_data()
    {
        $new_group_id = $this->get_id_reference($this->get_group_id(), $this->get_database_name() . '.group_info');
        $new_user_id = $this->get_id_reference($this->get_user_id(), 'main_database.user');

        $course_group_user_relation = new CourseGroupUserRelation();
        $course_group_user_relation->set_course_group($new_group_id);
        $course_group_user_relation->set_user($new_user_id);
        $course_group_user_relation->create();

    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }

    static function get_class_name()
    {
    	return self :: CLASS_NAME;
    }

}
?>