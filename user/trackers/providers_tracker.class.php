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
    const CLASS_NAME = __CLASS__;

    /**
     * Constructor sets the default values
     */
    function ProvidersTracker()
    {
        parent :: UserTracker();
        $this->set_property(self :: PROPERTY_TYPE, 'provider');
    }

    function track($parameters = array())
    {
        $server = $parameters['server'];
        $hostname = gethostbyaddr($server['REMOTE_ADDR']);
        $provider = $this->extract_provider($hostname);
        
        $conditions = array();
        $conditions[] = new EqualityCondition('type', 'provider');
        $conditions[] = new EqualityCondition('name', $provider);
        $condtion = new AndCondition($conditions);
        
        $trackeritems = $this->retrieve_tracker_items($condtion);
        if (count($trackeritems) != 0)
        {
            $providertracker = $trackeritems[0];
            $providertracker->set_value($providertracker->get_value() + 1);
            $providertracker->update();
        }
        else
        {
            $this->set_name($provider);
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
        $condition = new EqualityCondition('type', 'provider');
        return $this->remove($condition);
    }

    /**
     * Inherited
     */
    function export($start_date, $end_date, $event)
    {
        $condition = new EqualityCondition('type', 'provider');
        return $this->retrieve_tracker_items($condition);
    }

    /**
     * Extracts a provider from a given hostname
     * @param string $remhost The remote hostname
     * @return the provider
     */
    function extract_provider($remhost)
    {
        if ($remhost == "Unknown")
            return $remhost;
        
        $explodedRemhost = explode(".", $remhost);
        $provider = $explodedRemhost[sizeof($explodedRemhost) - 2] . "." . $explodedRemhost[sizeof($explodedRemhost) - 1];
        
        if ($provider == "co.uk" || $provider == "co.jp")
            return $explodedRemhost[sizeof($explodedRemhost) - 3] . $provider;
        else
            return $provider;
    
    }

    static function get_table_name()
    {
        return parent :: get_table_name();
    }
}
?>