<?php

/**
 * $Id: visit_tracker.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package users.lib.trackers
 */

/**
 * This class tracks the visits to pages
 */
class VisitTracker extends MainTracker
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_ENTER_DATE = 'enter_date';
    const PROPERTY_LEAVE_DATE = 'leave_date';
    const PROPERTY_LOCATION = 'location';
    const PROPERTY_TYPE = 'type';

    /**
     * Constructor sets the default values
     */
    function VisitTracker()
    {
        parent :: MainTracker('visit_tracker');
    }

    /**
     * Inherited
     * @see MainTracker :: track()
     */
    function track($parameters = array())
    {
        $user = $parameters['user'];
        $location = $parameters['location'];
        $type = $parameters['event'];
        $tracker = $parameters['tracker'];
        
        if ($user)
            $this->set_user_id($user->get_id());
        
        if ($type == 'leave')
        {
            //echo 'bus';
            $this->set_id($tracker);
            $this->set_leave_date(time());
            $this->update();
            echo $this->get_id();
        }
        else
        {
            $this->set_location($location);
            $this->set_enter_date(time());
            $this->set_leave_date(time());
            $this->create();
        }
        return $this->get_id();
    }

    /**
     * Inherited
     * @see MainTracker :: empty_tracker
     */
    function empty_tracker($event)
    {
        $condition = new EqualityCondition('type', $event->get_name());
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
        return $this->get_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Sets the userid of the visit tracker
     * @param int $userid the userid
     */
    function set_user_id($userid)
    {
        $this->set_property(self :: PROPERTY_USER_ID, $userid);
    }

    /**
     * Get's the enter date of the visit tracker
     * @return int $date the date
     */
    function get_enter_date()
    {
        return $this->get_property(self :: PROPERTY_ENTER_DATE);
    }

    /**
     * Sets the enter date of the visit tracker
     * @param int $date the date
     */
    function set_enter_date($value)
    {
        $this->set_property(self :: PROPERTY_ENTER_DATE, $value);
    }

    /**
     * Get's the leave date of the visit tracker
     * @return int $date the date
     */
    function get_leave_date()
    {
        return $this->get_property(self :: PROPERTY_LEAVE_DATE);
    }

    /**
     * Sets the leave date of the visit tracker
     * @param int $date the date
     */
    function set_leave_date($value)
    {
        $this->set_property(self :: PROPERTY_LEAVE_DATE, $value);
    }

    /**
     * Get's the location of the visit tracker
     * @return int $ip the ip
     */
    function get_location()
    {
        return $this->get_property(self :: PROPERTY_LOCATION);
    }

    /**
     * Sets the location of the visit tracker
     * @param int $ip the ip
     */
    function set_location($value)
    {
        $this->set_property(self :: PROPERTY_LOCATION, $value);
    }

    /**
     * Get's the type of the visit tracker
     * @return int $type the type
     */
    function get_type()
    {
        return $this->get_property(self :: PROPERTY_TYPE);
    }

    /**
     * Sets the type of the visit tracker
     * @param int $type the type
     */
    function set_type($type)
    {
        $this->set_property(self :: PROPERTY_TYPE, $type);
    }

    /**
     * Inherited
     */
    function get_default_property_names()
    {
        return array_merge(MainTracker :: get_default_property_names(), array(self :: PROPERTY_USER_ID, self :: PROPERTY_ENTER_DATE, self :: PROPERTY_LEAVE_DATE, self :: PROPERTY_LOCATION));
    }

    /**
     * Inherited
     * @see MainTracker :: is_summary_tracker
     */
    function is_summary_tracker()
    {
        return false;
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>