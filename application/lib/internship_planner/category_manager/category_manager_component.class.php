<?php

class InternshipPlannerCategoryManagerComponent extends SubManagerComponent
{

    function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_categories($condition, $offset, $count, $order_property);
    }

    function retrieve_category_rel_locations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_category_rel_locations($condition, $offset, $count, $order_property);
    }

    function retrieve_category_rel_location($location_id, $category_id)
    {
        return $this->get_parent()->retrieve_category_rel_location($location_id, $category_id);
    }

    function count_categories($conditions = null)
    {
        return $this->get_parent()->count_categories($conditions);
    }

    function count_category_rel_locations($conditions = null)
    {
        return $this->get_parent()->count_category_rel_locations($conditions);
    }
    
	function retrieve_full_category_rel_locations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_full_category_rel_locations($condition = null, $offset = null, $count = null, $order_property = null);
    }

    function count_full_category_rel_locations($conditions = null)
    {
        return $this->get_parent()->count_full_category_rel_locations($conditions);
    }
    

    function retrieve_category($id)
    {
        return $this->get_parent()->retrieve_category($id);
    }

    function retrieve_root_category()
    {
        return $this->get_parent()->retrieve_root_category();
    }

    //url
    

    function get_browse_categories_url()
    {
        return $this->get_parent()->get_browse_categories_url();
    }

    function get_category_editing_url($category)
    {
        return $this->get_parent()->get_category_editing_url($category);
    }

    function get_category_create_url()
    {
        return $this->get_parent()->get_category_create_url();
    
    }

    function get_create_category_url($parent)
    {
        return $this->get_parent()->get_create_category_url($parent);
    }

    function get_category_emptying_url($category)
    {
        return $this->get_parent()->get_category_emptying_url($category);
    }

    function get_category_viewing_url($category)
    {
        return $this->get_parent()->get_category_viewing_url($category);
    }

    function get_category_rel_location_unsubscribing_url($categoryrellocation)
    {
        return $this->get_parent()->get_category_rel_location_unsubscribing_url($categoryrellocation);
    }

    function get_category_rel_location_subscribing_url($category, $location)
    {
        return $this->get_parent()->get_category_rel_location_subscribing_url($category, $location);
    }

    function get_category_suscribe_location_browser_url($category)
    {
        return $this->get_parent()->get_category_suscribe_location_browser_url($category);
    }

    function get_category_delete_url($category)
    {
        return $this->get_parent()->get_category_delete_url($category);
    }

    function get_move_category_url($category)
    {
        return $this->get_parent()->get_move_category_url($category);
    }

}
?>