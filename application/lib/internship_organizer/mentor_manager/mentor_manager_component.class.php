<?php

class InternshipOrganizerMentorManagerComponent extends SubManagerComponent
{

    //mentor
    

    function count_mentors($condition)
    {
        return $this->get_parent()->count_mentors($condition);
    }

    function retrieve_mentors($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_mentors($condition, $offset, $count, $order_property);
    }

    function retrieve_mentor($id)
    {
        return $this->get_parent()->retrieve_mentor($id);
    }

    function get_create_mentor_url()
    {
        return $this->get_parent()->get_create_mentor_url();
    }

    function get_update_mentor_url($mentor)
    {
        return $this->get_parent()->get_update_mentor_url($mentor);
    }

    function get_delete_mentor_url($mentor)
    {
        return $this->get_parent()->get_delete_mentor_url($mentor);
    }

    function get_browse_mentors_url()
    {
        return $this->get_parent()->get_browse_mentors_url();
    }

    function get_view_mentor_url($mentor)
    {
        return $this->get_parent()->get_view_mentor_url($mentor);
    }
}
?>