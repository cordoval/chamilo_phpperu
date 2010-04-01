<?php
/**
 * @package group.lib
 *
 * This is an interface for a data manager for the Menu application.
 * Data managers must implement this class.
 *
 * @author Hans De Bisschop
 */
interface MenuDataManagerInterface
{

    function initialize();

    /**
     * Creates a storage unit
     * @param string $name Name of the storage unit
     * @param array $properties Properties of the storage unit
     * @param array $indexes The indexes which should be defined in the created
     * storage unit
     */
    abstract function create_storage_unit($name, $properties, $indexes);

    abstract function count_navigation_items($condition = null);

    abstract function retrieve_navigation_items($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function retrieve_navigation_item($id);

    abstract function retrieve_navigation_item_at_sort($parent, $sort, $direction);

    abstract function update_navigation_item($menuitem);

    abstract function delete_navigation_items($condition = null);
}
?>