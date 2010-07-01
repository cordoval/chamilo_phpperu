<?php
/**
 * This class tracks the country that a user uses
 * @package user.trackers
 */
require_once dirname(__FILE__) . '/user_tracker.class.php';

class CountriesTracker extends UserTracker
{

    function validate_parameters(array $parameters = array())
    {
        $server = $parameters['server'];
        $hostname = gethostbyaddr($server['REMOTE_ADDR']);
        $country = $this->extract_country($hostname);

        $this->set_type(self :: TYPE_COUNTRY);
        $this->set_name($country);
    }

    /**
     * Inherited
     * @see MainTracker :: empty_tracker
     */
    function empty_tracker($event)
    {
        $condition = new EqualityCondition(self :: PROPERTY_TYPE, 'country');
        return $this->remove($condition);
    }

    /**
     * Inherited
     */
    function export($start_date, $end_date, $event)
    {
        $condition = new EqualityCondition(self :: PROPERTY_TYPE, 'country');
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
        {
            return $remhost;
        }

        // country code is the last value of remote host
        $explodedRemhost = explode(".", $remhost);
        $code = $explodedRemhost[sizeof($explodedRemhost) - 1];

        if ($code == 'localhost')
        {
            return "Unknown";
        }
        else
        {
            return $code;
        }
    }
}
?>