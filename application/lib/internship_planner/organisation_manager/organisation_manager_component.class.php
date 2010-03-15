<?php

class InternshipOrganisationManagerComponent extends SubManagerComponent
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


}
?>