<?php
/**
 * @package group.lib
 *
 * This is an interface for a data manager for the Home application.
 * Data managers must implement this class.
 *
 * @author Hans De Bisschop
 */
interface HomeDataManagerInterface
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

    function count_home_rows($condition = null);

    function count_home_columns($condition = null);

    function count_home_blocks($condition = null);

    function retrieve_home_rows($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_home_column($id);

    function retrieve_home_columns($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_home_block($id);

    function retrieve_home_blocks($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_home_tab($id);

    function retrieve_home_tab_blocks($home_tab);

    function retrieve_home_tabs($condition = null, $offset = null, $count = null, $order_property = null);

    function truncate_home($user_id);

    function retrieve_home_row_at_sort($parent, $sort, $direction);

    function retrieve_home_column_at_sort($parent, $sort, $direction);

    function retrieve_home_block_at_sort($parent, $sort, $direction);

    function retrieve_home_tab_at_sort($user, $sort, $direction);

    function update_home_block($home_block);

    function update_home_block_config($home_block_config);

    function update_home_column($home_column);

    function update_home_row($home_row);

    function update_home_tab($home_tab);

    function create_home_row($home_row);

    function create_home_column($home_column);

    function create_home_block($home_block);

    function create_home_block_config($home_block_config);

    function delete_home_row($home_row);

    function delete_home_tab($home_tab);

    function delete_home_column($home_column);

    function delete_home_block($home_block);

    function delete_home_block_config($home_block_config);

    function delete_home_block_configs($home_block);

    function retrieve_home_block_config($condition = null, $offset = null, $count = null, $order_property = null);

    function count_home_block_config($condition = null);

}
?>