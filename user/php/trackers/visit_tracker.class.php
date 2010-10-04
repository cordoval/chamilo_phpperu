<?php
/**
 * This class tracks the visits to pages
 * 
 * @package users.lib.trackers
 */

class VisitTracker extends SimpleTracker
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_ENTER_DATE = 'enter_date';
    const PROPERTY_LEAVE_DATE = 'leave_date';
    const PROPERTY_LOCATION = 'location';
    
    const TYPE_ENTER = 'enter';
    const TYPE_LEAVE = 'leave';

    function validate_parameters(array $parameters = array())
    {
        $type = $this->get_event()->get_name();
        
        if ($parameters[self :: PROPERTY_USER_ID])
        {
            $this->set_user_id($parameters[self :: PROPERTY_USER_ID]);
        }
        
        if ($type == self :: TYPE_LEAVE)
        {
            $this->set_id($parameters[self :: PROPERTY_ID]);
            $this->set_leave_date(time());
        }
        else
        {
            $this->set_location($parameters[self :: PROPERTY_LOCATION]);
            $this->set_enter_date(time());
            $this->set_leave_date(time());
        }
    }

    function run(array $parameters = array())
    {
        $this->validate_parameters($parameters);
        
        $type = $this->get_event()->get_name();
        
        if ($type == self :: TYPE_LEAVE)
        {
            return $this->update();
        }
        else
        {
            return $this->create();
        }
    }

    /**
     * Inherited
     * @see MainTracker :: empty_tracker
     */
    function empty_tracker()
    {
        $condition = new EqualityCondition(self :: PROPERTY_TYPE, $this->get_event()->get_name());
        return $this->remove($condition);
    }

    /**
     * Inherited
     */
    function export($start_date, $end_date, $event)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition('type', $event->get_name());
        return parent :: export($start_date, $end_date, $conditions);
    }

    /**
     * Get's the userid of the visit tracker
     * @return int $userid the userid
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Sets the userid of the visit tracker
     * @param int $userid the userid
     */
    function set_user_id($userid)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $userid);
    }

    /**
     * Get's the enter date of the visit tracker
     * @return int $date the date
     */
    function get_enter_date()
    {
        return $this->get_default_property(self :: PROPERTY_ENTER_DATE);
    }

    /**
     * Sets the enter date of the visit tracker
     * @param int $date the date
     */
    function set_enter_date($value)
    {
        $this->set_default_property(self :: PROPERTY_ENTER_DATE, $value);
    }

    /**
     * Get's the leave date of the visit tracker
     * @return int $date the date
     */
    function get_leave_date()
    {
        return $this->get_default_property(self :: PROPERTY_LEAVE_DATE);
    }

    /**
     * Sets the leave date of the visit tracker
     * @param int $date the date
     */
    function set_leave_date($value)
    {
        $this->set_default_property(self :: PROPERTY_LEAVE_DATE, $value);
    }

    /**
     * Get's the location of the visit tracker
     * @return int $ip the ip
     */
    function get_location()
    {
        return $this->get_default_property(self :: PROPERTY_LOCATION);
    }

    /**
     * Sets the location of the visit tracker
     * @param int $ip the ip
     */
    function set_location($value)
    {
        $this->set_default_property(self :: PROPERTY_LOCATION, $value);
    }

    /**
     * Inherited
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_USER_ID, self :: PROPERTY_ENTER_DATE, self :: PROPERTY_LEAVE_DATE, self :: PROPERTY_LOCATION));
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>