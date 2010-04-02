<?php

class InternshipOrganizerAgreementManagerComponent extends SubManagerComponent
{

    //agreement
    

    function count_agreements($condition)
    {
        return $this->get_parent()->count_agreements($condition);
    }

    function retrieve_agreements($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_agreements($condition, $offset, $count, $order_property);
    }

    function retrieve_agreement($id)
    {
        return $this->get_parent()->retrieve_agreement($id);
    }

    function get_create_agreement_url()
    {
        return $this->get_parent()->get_create_agreement_url();
    }

    function get_update_agreement_url($agreement)
    {
        return $this->get_parent()->get_update_agreement_url($agreement);
    }

    function get_delete_agreement_url($agreement)
    {
        return $this->get_parent()->get_delete_agreement_url($agreement);
    }

    function get_browse_agreements_url()
    {
        return $this->get_parent()->get_browse_agreements_url();
    }

    function get_view_agreement_url($agreement)
    {
        return $this->get_parent()->get_view_agreement_url($agreement);
    }

    //moments
    

    function count_moments($condition)
    {
        return $this->get_parent()->count_moments($condition);
    }

    function retrieve_moments($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_moments($condition, $offset, $count, $order_property);
    }

    function retrieve_moment($id)
    {
        return $this->get_parent()->retrieve_moment($id);
    }

    function get_create_moment_url($moment)
    {
        return $this->get_parent()->get_create_moment_url($moment);
    }

    function get_update_moment_url($moment)
    {
        return $this->get_parent()->get_update_moment_url($moment);
    }

    function get_delete_moment_url($moment)
    {
        return $this->get_parent()->get_delete_moment_url($moment);
    }

    function get_browse_moments_url()
    {
        return $this->get_parent()->get_browse_moments_url();
    }
}
?>