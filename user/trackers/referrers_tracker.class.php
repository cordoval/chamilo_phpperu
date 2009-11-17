<?php
/**
 * $Id: referrers_tracker.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package users.lib.trackers
 */

require_once dirname(__FILE__) . '/user_tracker.class.php';

/**
 * This class tracks the referer that a user uses
 */
class ReferrersTracker extends UserTracker
{
    const CLASS_NAME = __CLASS__;

    /**
     * Constructor sets the default values
     */
    function ReferrersTracker()
    {
        parent :: UserTracker();
        $this->set_property(self :: PROPERTY_TYPE, 'referer');
    }

    function track($parameters = array())
    {
        $server = $parameters['server'];
        $referer = $server['HTTP_REFERER'];
        
        $conditions = array();
        $conditions[] = new EqualityCondition('type', 'referer');
        $conditions[] = new EqualityCondition('name', $referer);
        $condtion = new AndCondition($conditions);
        
        $trackeritems = $this->retrieve_tracker_items($condtion);
        if (count($trackeritems) != 0)
        {
            $referertracker = $trackeritems[0];
            $referertracker->set_value($referertracker->get_value() + 1);
            $referertracker->update();
        }
        else
        {
            $this->set_name($referer);
            $this->set_value(1);
            $this->create();
        }
    }

    /**
     * Inherited
     * @see MainTracker :: empty_tracker
     */
    function empty_tracker($event)
    {
        $condition = new EqualityCondition('type', 'referer');
        return $this->remove($condition);
    }

    /**
     * Inherited
     */
    function export($start_date, $end_date)
    {
        $condition = new EqualityCondition('type', 'referer');
        return $this->retrieve_tracker_items($condition);
    }

    static function get_table_name()
    {
        return parent :: get_table_name();
    }
}
?>