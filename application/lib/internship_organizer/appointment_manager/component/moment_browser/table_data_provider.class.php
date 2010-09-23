<?php

class InternshipOrganizerMomentRelUserBrowserTableDataProvider extends ObjectTableDataProvider
{

    function InternshipOrganizerMomentRelUserBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    function get_objects($offset, $count, $order_property = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_moment_rel_users($this->get_condition(), $offset, $count, $order_property);
    }

    function get_object_count()
    {
        return InternshipOrganizerDataManager :: get_instance()->count_moment_rel_users($this->get_condition());
    }
}
?>