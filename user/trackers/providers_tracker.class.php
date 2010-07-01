<?php
/**
 * $Id: providers_tracker.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.trackers
 */

require_once dirname(__FILE__) . '/user_tracker.class.php';

/**
 * This class tracks the provider that a user uses
 */
class ProvidersTracker extends UserTracker
{

    function validate_parameters(array $parameters = array())
    {
        $server = $parameters['server'];
        $hostname = gethostbyaddr($server['REMOTE_ADDR']);
        $provider = $this->extract_provider($hostname);

        $this->set_type(self :: TYPE_PROVIDER);
        $this->set_name($provider);
    }

    /**
     * Inherited
     * @see MainTracker :: empty_tracker
     */
    function empty_tracker($event)
    {
        $condition = new EqualityCondition(self :: PROPERTY_TYPE, self :: TYPE_PROVIDER);
        return $this->remove($condition);
    }

    /**
     * Inherited
     */
    function export($start_date, $end_date, $event)
    {
        $condition = new EqualityCondition(self :: PROPERTY_TYPE, self :: TYPE_PROVIDER);
        return $this->get_data_manager()->retrieve_tracker_items($this->get_table_name(), $condition);
    }

    /**
     * Extracts a provider from a given hostname
     * @param string $remhost The remote hostname
     * @return the provider
     */
    function extract_provider($remhost)
    {
        if ($remhost == "Unknown")
        {
            return $remhost;
        }

        $explodedRemhost = explode(".", $remhost);
        $provider = $explodedRemhost[sizeof($explodedRemhost) - 2] . "." . $explodedRemhost[sizeof($explodedRemhost) - 1];

        if ($provider == "co.uk" || $provider == "co.jp")
        {
            return $explodedRemhost[sizeof($explodedRemhost) - 3] . $provider;
        }
        else
        {
            return $provider;
        }

    }
}
?>