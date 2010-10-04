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
    function create_storage_unit($name, $properties, $indexes);

    function count_navigation_items($condition = null);

    function retrieve_navigation_items($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_navigation_item($id);

    function retrieve_navigation_item_at_sort($parent, $sort, $direction);

    function update_navigation_item($menuitem);

    function delete_navigation_items($condition = null);
}
?>