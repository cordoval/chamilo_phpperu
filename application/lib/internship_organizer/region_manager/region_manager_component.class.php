<?php

class InternshipOrganizerRegionManagerComponent extends SubManagerComponent
{

    function retrieve_regions($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_regions($condition, $offset, $count, $order_property);
    }
    
    function count_regions($conditions = null)
    {
        return $this->get_parent()->count_regions($conditions);
    }
    
    function retrieve_region($id)
    {
        return $this->get_parent()->retrieve_region($id);
    }

    function retrieve_root_region()
    {
        return $this->get_parent()->retrieve_root_region();
    }

    //url
    

    function get_browse_regions_url()
    {
        return $this->get_parent()->get_browse_regions_url();
    }

    function get_region_editing_url($region)
    {
        return $this->get_parent()->get_region_editing_url($region);
    }

    function get_region_create_url($parent=null)
    {
        return $this->get_parent()->get_region_create_url($parent);
    }

    function get_region_emptying_url($region)
    {
        return $this->get_parent()->get_region_emptying_url($region);
    }

    function get_region_viewing_url($region)
    {
        return $this->get_parent()->get_region_viewing_url($region);
    }

    function get_region_delete_url($region)
    {
        return $this->get_parent()->get_region_delete_url($region);
    }


}
?>