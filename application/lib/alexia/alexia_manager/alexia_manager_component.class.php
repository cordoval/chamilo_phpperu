<?php
/**
 * $Id: alexia_manager_component.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.alexia.alexia_manager
 */

abstract class AlexiaManagerComponent extends WebApplicationComponent
{

    /**
     * Constructor
     * @param AlexiaManager $pcm The personal calendar manager which provides this component
     */
    protected function AlexiaManagerComponent($am)
    {
        parent :: __construct($am);
    }

    /**
     * @see AlexiaManager :: count_alexia_publications
     */
    function count_alexia_publications($condition = null)
    {
        return $this->get_parent()->count_alexia_publications($condition);
    }

    /**
     * @see AlexiaManager :: retrieve_alexia_publication()
     */
    function retrieve_alexia_publication($id)
    {
        return $this->get_parent()->retrieve_alexia_publication($id);
    }

    /**
     * @see AlexiaManager :: retrieve_alexia_publications()
     */
    function retrieve_alexia_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->get_parent()->retrieve_alexia_publications($condition, $order_by, $offset, $max_objects);
    }

    function get_publication_viewing_url($alexia_publication)
    {
        return $this->get_parent()->get_publication_viewing_url($alexia_publication);
    }

    function get_publication_editing_url($alexia_publication)
    {
        return $this->get_parent()->get_publication_editing_url($alexia_publication);
    }

    function get_introduction_editing_url($introduction)
    {
        return $this->get_parent()->get_introduction_editing_url($introduction);
    }

    function get_publication_deleting_url($alexia_publication)
    {
        return $this->get_parent()->get_publication_deleting_url($alexia_publication);
    }
}
?>