<?php
/**
 * $Id: location_manager_component.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.location_manager
 */
class LocationManagerComponent extends SubManagerComponent
{

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
        return $this->get_parent()->get_location_inheriting_url($location);
    }

    function get_location_disinheriting_url($location)
    {
        return $this->get_parent()->get_location_disinheriting_url($location);
    }

    function get_location_locking_url($location)
    {
        return $this->get_parent()->get_location_locking_url($location);
    }

    function get_location_unlocking_url($location)
    {
        return $this->get_parent()->get_location_unlocking_url($location);
    }
}
?>