<?php

class InternshipPlannerOrganisationManagerComponent extends SubManagerComponent
{

    //organisation
    

    function count_organisations($condition)
    {
        return $this->get_parent()->count_organisations($condition);
    }

    function retrieve_organisations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_organisations($condition, $offset, $count, $order_property);
    }

    function retrieve_organisation($id)
    {
        return $this->get_parent()->retrieve_organisation($id);
    }

    function get_create_organisation_url()
    {
        return $this->get_parent()->get_create_organisation_url();
    }

    function get_update_organisation_url($organisation)
    {
        return $this->get_parent()->get_update_organisation_url($organisation);
    }

    function get_delete_organisation_url($organisation)
    {
        return $this->get_parent()->get_delete_organisation_url($organisation);
    }

    function get_browse_organisations_url()
    {
        return $this->get_parent()->get_browse_organisations_url();
    }

    function get_view_organisation_url($organisation)
    {
        return $this->get_parent()->get_view_organisation_url($organisation);
    }

    //locations
    

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

    function get_create_location_url($organisation)
    {
        return $this->get_parent()->get_create_location_url($organisation);
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