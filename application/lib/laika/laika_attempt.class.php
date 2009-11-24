<?php
/**
 * $Id: laika_attempt.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika
 */

class LaikaAttempt
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'attempt';

    const PROPERTY_ID = 'id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_DATE = 'date';

    private $defaultProperties;

    private $user;

    function __construct($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets the value of a given default property
     * @param String $name the name of the default property
     * @return Object $value the new value
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gives a value to a given default property
     * @param String $name the name of the default property
     * @param Object $value the new value
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Retrieves the default properties of this class
     * @return Array of Objects
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Sets the default properties of this class
     * @param Array Of Objects $defaultProperties
     */
    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Method to check whether a certain name is a default property name
     * @param String $name
     * @return
     */
    static function is_default_property_name($name)
    {
        return in_array($name, self :: get_default_property_names());
    }

    /**
     * Get the default property names
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_DATE);
    }

    function create()
    {
        $ldm = LaikaDataManager :: get_instance();
        $this->set_date(time());
        return $ldm->create_laika_attempt($this);
    }

    function update()
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->update_laika_attempt($this);
    }

    function delete()
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->delete_laika_attempt($this);
    }

    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_date()
    {
        return $this->get_default_property(self :: PROPERTY_DATE);
    }

    function set_date($date)
    {
        $this->set_default_property(self :: PROPERTY_DATE, $date);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }

    function get_user()
    {
        if (! isset($this->user))
        {
            $user_id = $this->get_user_id();
            $udm = UserDataManager :: get_instance();
            $this->user = $udm->retrieve_user($user_id);
        }

        return $this->user;
    }
}
?>