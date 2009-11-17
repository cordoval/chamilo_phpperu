<?php
/**
 * $Id: countries_tracker.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.trackers
 */
require_once dirname(__FILE__) . '/user_tracker.class.php';

/**
 * This class tracks the country that a user uses
 */
class CountriesTracker extends UserTracker
{
    const CLASS_NAME = __CLASS__;

    /**
     * Constructor sets the default values
     */
    function CountriesTracker()
    {
        parent :: UserTracker();
        $this->set_property(self :: PROPERTY_TYPE, 'country');
    }

    function track($parameters = array())
    {
        $server = $parameters['server'];
        $hostname = gethostbyaddr($server['REMOTE_ADDR']);
        $country = $this->extract_country($hostname);
        
        $conditions = array();
        $conditions[] = new EqualityCondition('type', 'country');
        $conditions[] = new EqualityCondition('name', $country);
        $condtion = new AndCondition($conditions);
        
        $trackeritems = $this->retrieve_tracker_items($condtion);
        if (count($trackeritems) != 0)
        {
            $countrytracker = $trackeritems[0];
            $countrytracker->set_value($countrytracker->get_value() + 1);
            $countrytracker->update();
        }
        else
        {
            $this->set_name($country);
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
        $condition = new EqualityCondition('type', 'country');
        return $this->remove($condition);
    }

    /**
     * Inherited
     */
    function export($start_date, $end_date, $event)
    {
        $condition = new EqualityCondition('type', 'country');
        return $this->retrieve_tracker_items($condition);
    }

    /**
     * Extracts the country code from the remote host
     * @param Remote Host $remhost instance of $_SERVER['REMOTE_ADDR']
     * @return string country code
     */
    function extract_country($remhost)
    {
        if ($remhost == "Unknown")
            return $remhost;
            
        // country code is the last value of remote host
        $explodedRemhost = explode(".", $remhost);
        $code = $explodedRemhost[sizeof($explodedRemhost) - 1];
        
        if ($code == 'localhost')
            return "Unknown";
        else
            return $code;
    }

    static function get_table_name()
    {
        return parent :: get_table_name();
    }
}
?>