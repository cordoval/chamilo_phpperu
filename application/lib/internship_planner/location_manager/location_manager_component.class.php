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

}
?>