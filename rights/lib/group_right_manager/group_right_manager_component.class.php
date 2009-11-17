<?php
/**
 * $Id: group_right_manager_component.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.group_right_manager
 */
class GroupRightManagerComponent extends SubManagerComponent
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

    function retrieve_group_right_location($right_id, $group_id, $location_id)
    {
        return $this->get_parent()->retrieve_group_right_location($right_id, $group_id, $location_id);
    }

    function retrieve_groups($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_groups($condition, $offset, $count, $order_property);
    }

    function count_groups($conditions = null)
    {
        return $this->get_parent()->count_groups($conditions);
    }
}
?>