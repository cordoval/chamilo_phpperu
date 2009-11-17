<?php
/**
 * $Id: home_manager_component.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.lib.home_manager
 */
/**
 * Base class for a user manager component.
 * A user manager provides different tools to the end user. Each tool is
 * represented by a user manager component and should extend this class.
 */

abstract class HomeManagerComponent extends CoreApplicationComponent
{

    /**
     * Constructor
     * @param MenuManager $menumanager The menumanager which
     * provides this component
     */
    function HomeManagerComponent($home_manager)
    {
        parent :: __construct($home_manager);
    }

    function count_home_rows($conditions = null)
    {
        return $this->get_parent()->count_home_rows($conditions);
    }

    function count_home_columns($conditions = null)
    {
        return $this->get_parent()->count_home_columns($conditions);
    }

    function count_home_blocks($conditions = null)
    {
        return $this->get_parent()->count_home_blocks($conditions);
    }

    function display_popup_form($form_html)
    {
        $this->get_parent()->display_popup_form($form_html);
    }

    function get_search_condition()
    {
        return $this->get_parent()->get_search_condition();
    }

    function retrieve_home_tabs($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_home_tabs($condition, $offset, $count, $order_property);
    }

    function retrieve_home_rows($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_home_rows($condition, $offset, $count, $order_property);
    }

    function retrieve_home_columns($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_home_columns($condition, $offset, $count, $order_property);
    }

    function retrieve_home_blocks($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_home_blocks($condition, $offset, $count, $order_property);
    }

    function retrieve_home_block($id)
    {
        return $this->get_parent()->retrieve_home_block($id);
    }

    function retrieve_home_column($id)
    {
        return $this->get_parent()->retrieve_home_column($id);
    }

    function retrieve_home_row($id)
    {
        return $this->get_parent()->retrieve_home_row($id);
    }

    function retrieve_home_tab($id)
    {
        return $this->get_parent()->retrieve_home_tab($id);
    }

    function retrieve_home_block_config($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_home_block_config($condition, $offset, $count, $order_property);
    }

    function truncate_home($user_id)
    {
        return $this->get_parent()->truncate_home($user_id);
    }

    function get_home_tab_editing_url($home_tab)
    {
        return $this->get_parent()->get_home_tab_editing_url($home_tab);
    }

    function get_home_row_editing_url($home_row)
    {
        return $this->get_parent()->get_home_row_editing_url($home_row);
    }

    function get_home_column_editing_url($home_column)
    {
        return $this->get_parent()->get_home_column_editing_url($home_column);
    }

    function get_home_block_editing_url($home_block)
    {
        return $this->get_parent()->get_home_block_editing_url($home_block);
    }

    function get_home_block_configuring_url($home_block)
    {
        return $this->get_parent()->get_home_block_configuring_url($home_block);
    }

    function get_home_tab_creation_url()
    {
        return $this->get_parent()->get_home_tab_creation_url();
    }

    function get_home_row_creation_url()
    {
        return $this->get_parent()->get_home_row_creation_url();
    }

    function get_home_column_creation_url()
    {
        return $this->get_parent()->get_home_column_creation_url();
    }

    function get_home_block_creation_url()
    {
        return $this->get_parent()->get_home_block_creation_url();
    }

    function get_home_tab_deleting_url($home_tab)
    {
        return $this->get_parent()->get_home_tab_deleting_url($home_tab);
    }

    function get_home_row_deleting_url($home_row)
    {
        return $this->get_parent()->get_home_row_deleting_url($home_row);
    }

    function get_home_column_deleting_url($home_column)
    {
        return $this->get_parent()->get_home_column_deleting_url($home_column);
    }

    function get_home_block_deleting_url($home_block)
    {
        return $this->get_parent()->get_home_block_deleting_url($home_block);
    }

    function get_home_tab_moving_url($home_tab, $index)
    {
        return $this->get_parent()->get_home_tab_moving_url($home_tab, $index);
    }

    function get_home_row_moving_url($home_row, $index)
    {
        return $this->get_parent()->get_home_row_moving_url($home_row, $index);
    }

    function get_home_tab_viewing_url($home_tab)
    {
        return $this->get_parent()->get_home_tab_viewing_url($home_tab);
    }

    function get_home_block_moving_url($home_block, $index)
    {
        return $this->get_parent()->get_home_block_moving_url($home_block, $index);
    }

    function get_home_column_moving_url($home_column, $index)
    {
        return $this->get_parent()->get_home_column_moving_url($home_column, $index);
    }

    function retrieve_home_block_at_sort($parent, $sort, $direction)
    {
        return $this->get_parent()->retrieve_home_block_at_sort($parent, $sort, $direction);
    }

    function retrieve_home_column_at_sort($parent, $sort, $direction)
    {
        return $this->get_parent()->retrieve_home_column_at_sort($parent, $sort, $direction);
    }

    function retrieve_home_row_at_sort($parent, $sort, $direction)
    {
        return $this->get_parent()->retrieve_home_row_at_sort($parent, $sort, $direction);
    }

    function retrieve_home_tab_at_sort($user, $sort, $direction)
    {
        return $this->get_parent()->retrieve_home_tab_at_sort($user, $sort, $direction);
    }
}
?>