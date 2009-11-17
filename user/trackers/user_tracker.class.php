<?php
/**
 * $Id: user_tracker.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package users.lib.trackers
 */


/**
 * This class is a abstract class for user tracking
 */
abstract class UserTracker extends MainTracker
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_TYPE = 'type';
    const PROPERTY_NAME = 'name';
    const PROPERTY_VALUE = 'value';

    /**
     * Constructor sets the default values
     */
    function UserTracker()
    {
        parent :: MainTracker('user_tracker');
    }

    /**
     * Inherited
     */
    function get_default_property_names()
    {
        return array_merge(MainTracker :: get_default_property_names(), array(self :: PROPERTY_TYPE, self :: PROPERTY_NAME, self :: PROPERTY_VALUE));
    }

    /**
     * Get's the type of the user tracker
     * @return int $type the type
     */
    function get_type()
    {
        return $this->get_property(self :: PROPERTY_TYPE);
    }

    /**
     * Sets the type of the user tracker
     * @param int $type the type
     */
    function set_type($type)
    {
        $this->set_property(self :: PROPERTY_TYPE, $type);
    }

    /**
     * Get's the name of the user tracker
     * @return int $name the name
     */
    function get_name()
    {
        return $this->get_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name of the user tracker
     * @param int $name the name
     */
    function set_name($name)
    {
        $this->set_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Get's the value of the user tracker
     * @return int $value the value
     */
    function get_value()
    {
        return $this->get_property(self :: PROPERTY_VALUE);
    }

    /**
     * Sets the value of the user tracker
     * @param int $value the value
     */
    function set_value($value)
    {
        $this->set_property(self :: PROPERTY_VALUE, $value);
    }

    /**
     * Inherited
     * @see MainTracker :: is_summary_tracker
     */
    function is_summary_tracker()
    {
        return true;
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>