<?php

/**
 * $Id: dokeos185_group_rel_user.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_group_rel_user.class.php';

/**
 * This class represents an old Dokeos 1.8.5 Group Tutor Relation
 *
 * @author David Van Wayenbergh
 */

class Dokeos185GroupRelUser extends MigrationDataClass
{
    private static $mgdm;
    
    /**
     * group tutor relation properties
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
    function is_valid($array)
    {
        $course = $array['course'];
    }

    /**
     * Convert to new group user relation 
     * @param Course $course the course
     * @return the new group user relation
     */
    function convert_data
    {
        $course = $array['course'];
    }

    /**
     * Retrieve all group user relations from the database
     * @param array $parameters parameters for the retrieval
     * @return array of group user relations
     */
    static function retrieve_data($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $coursedb = $parameters['course']->get_db_name();
        $tablename = 'group_rel_user';
        $classname = 'Dokeos185GroupRelUser';
        
        return self :: $mgdm->get_all($coursedb, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'group_rel_user';
        return $array;
    }
}
?>