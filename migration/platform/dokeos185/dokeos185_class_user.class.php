<?php
/**
 * $Id: dokeos185_class_user.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_class_user.class.php';

/**
 * This class represents an old Dokeos 1.8.5 class
 *
 * @author David Van Wayenbergh
 * @author Sven Vanpoucke
 */

class Dokeos185ClassUser extends ImportClassUser
{
    
    /**
     * class relation user properties
     */
    
    const PROPERTY_CLASS_ID = 'class_id';
    const PROPERTY_USER_ID = 'user_id';
    
    /**
     * Alfanumeric identifier of the class_user object.
     */
    private $code;
    
    /**
     * Default properties of the class_user object, stored in an associative
     * array.
     */
    private $defaultProperties;

    /**
     * Creates a new class_user object.
     * @param array $defaultProperties The default properties of the class_user
     *                                 object. Associative array.
     */
    function Dokeos185ClassUser($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property of this class_user object by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties of this class_user.
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the default properties of all classe_users.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_CLASS_ID, self :: PROPERTY_USER_ID);
    }

    /**
     * Sets a default property of this class_user by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Checks if the given identifier is the name of a default class_user
     * property.
     * @param string $name The identifier.
     * @return boolean True if the identifier is a property name, false
     *                 otherwise.
     */
    static function is_default_property_name($name)
    {
        return in_array($name, self :: get_default_property_names());
    }

    /**
     * Sets the default properties of this class
     */
    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Returns the class_id of this class_user.
     * @return int The id.
     */
    function get_class_id()
    {
        return $this->get_default_property(self :: PROPERTY_CLASS_ID);
    }

    /**
     * Returns the code of this class_user.
     * @return int The code.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Check if the class user is valid
     * @param array $array the parameters for the validation
     * @return true if the blog is valid 
     */
    function is_valid($parameters)
    {
        $mgdm = MigrationDataManager :: get_instance();
        if (! $this->get_class_id() || ! $this->get_user_id() || $mgdm->get_failed_element('dokeos_main.class', $this->get_class_id()) || $mgdm->get_failed_element('dokeos_main.user', $this->get_user_id()) || ! $mgdm->get_id_reference($this->get_user_id(), 'user_user'))
        {
            $mgdm->add_failed_element($this->get_class_id() . '-' . $this->get_user_id(), 'dokeos_main.class_user');
            return false;
        }
        
        return true;
    }

    /**
     * Convert to new class user
     * @param array $array the parameters for the conversion
     * @return the new course
     */
    function convert_to_lcms($parameters)
    {
        $mgdm = MigrationDataManager :: get_instance();
        $lcms_class_user = new GroupRelUser();
        
        $class_id = $mgdm->get_id_reference($this->get_class_id(), 'classgroup_classgroup');
        if ($class_id)
            $lcms_class_user->set_group_id($class_id);
        
        $user_id = $mgdm->get_id_reference($this->get_user_id(), 'user_user');
        if ($user_id)
            $lcms_class_user->set_user_id($user_id);
        
        $lcms_class_user->create();
        
        return $lcms_class_user;
    }

    /**
     * Retrieve all class users from the database
     * @param array $parameters parameters for the retrieval
     * @return array of class users
     */
    static function get_all($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        $db = 'main_database';
        $tablename = 'class_user';
        $classname = 'Dokeos185ClassUser';
        
        return $old_mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'main_database';
        $array['table'] = 'class_user';
        return $array;
    }
}
?>