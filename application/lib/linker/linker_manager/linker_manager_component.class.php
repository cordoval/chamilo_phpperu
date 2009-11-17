<?php
/**
 * $Id: linker_manager_component.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.linker.linker_manager
 */

abstract class LinkerManagerComponent extends WebApplicationComponent
{

    /**
     * Constructor
     * @param LinkerManager $linker The linker manager which
     * provides this component
     */
    protected function LinkerManagerComponent($linker)
    {
        parent :: __construct($linker);
    }

    function count_links($condition)
    {
        return $this->get_parent()->count_links($condition);
    }

    function retrieve_links($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_links($condition, $offset, $count, $order_property);
    }

    function retrieve_link($id)
    {
        return $this->get_parent()->retrieve_link($id);
    }

    // Url Creation
    function get_create_link_url()
    {
        return $this->get_parent()->get_create_link_url();
    }

    function get_update_link_url($link)
    {
        return $this->get_parent()->get_update_link_url($link);
    }

    function get_delete_link_url($link)
    {
        return $this->get_parent()->get_delete_link_url($link);
    }
}
?>