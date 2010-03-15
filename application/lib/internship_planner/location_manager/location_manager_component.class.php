<?php

class InternshipLocationManagerComponent extends SubManagerComponent
{

    //location
    

    function count_locations($condition)
    {
        return $this->get_parent()->count_locations($condition);
    }

    function retrieve_locations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_locations($condition, $offset, $count, $order_property);
    }

    function retrieve_location($id)
    {
        return $this->get_parent()->retrieve_location($id);
    }
    
	function get_create_location_url()
	{
		return $this->get_parent()->get_create_location_url();
	}

	function get_update_location_url($location)
	{
		return $this->get_parent()->get_update_location_url($location);
	}

 	function get_delete_location_url($location)
	{
		return $this->get_parent()->get_delete_location_url($location);
	}

	function get_browse_locations_url()
	{
		return $this->get_parent()->get_browse_locations_url();
	}


}
?>