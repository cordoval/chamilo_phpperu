<?php
/**
 * $Id: location_manager.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.location_manager
 */

class LocationManager extends SubManager
{
    const PARAM_LOCATION_ACTION = 'action';
    const PARAM_SOURCE = 'source';
    const PARAM_LOCATION = 'location';
    
    const ACTION_BROWSE_LOCATIONS = 'browser';
    const ACTION_LOCK_LOCATIONS = 'locker';
    const ACTION_UNLOCK_LOCATIONS = 'unlocker';
    const ACTION_INHERIT_LOCATIONS = 'inheriter';
    const ACTION_DISINHERIT_LOCATIONS = 'disinheriter';
    
    const DEFAULT_ACTION = self :: ACTION_BROWSE_LOCATIONS;

    function LocationManager($rights_manager)
    {
        parent :: __construct($rights_manager);
        
        $location_action = Request :: get(self :: PARAM_LOCATION_ACTION);
        if ($location_action)
        {
            $this->set_parameter(self :: PARAM_LOCATION_ACTION, $location_action);
        }
    }

    function get_application_component_path()
    {
        return Path :: get_rights_path() . 'lib/location_manager/component/';
    }

    function retrieve_locations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_locations($condition, $offset, $count, $order_property);
    }

    function count_locations($conditions = null)
    {
        return $this->get_parent()->count_locations($conditions);
    }

    function retrieve_location($location_id)
    {
        return $this->get_parent()->retrieve_location($location_id);
    }

    function get_location_inheriting_url($location)
    {
        return $this->get_url(array(LocationManager :: PARAM_LOCATION_ACTION => LocationManager :: ACTION_INHERIT_LOCATIONS, LocationManager :: PARAM_LOCATION => $location->get_id()));
    }

    function get_location_disinheriting_url($location)
    {
        return $this->get_url(array(LocationManager :: PARAM_LOCATION_ACTION => LocationManager :: ACTION_DISINHERIT_LOCATIONS, LocationManager :: PARAM_LOCATION => $location->get_id()));
    }

    function get_location_locking_url($location)
    {
        return $this->get_url(array(LocationManager :: PARAM_LOCATION_ACTION => LocationManager :: ACTION_LOCK_LOCATIONS, LocationManager :: PARAM_LOCATION => $location->get_id()));
    }

    function get_location_unlocking_url($location)
    {
        return $this->get_url(array(LocationManager :: PARAM_LOCATION_ACTION => LocationManager :: ACTION_UNLOCK_LOCATIONS, LocationManager :: PARAM_LOCATION => $location->get_id()));
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_LOCATION_ACTION;
    }

    /**
     * @param Application $application
     */
    static function launch($application)
    {
        parent :: launch(__CLASS__, $application);
    }
}
?>