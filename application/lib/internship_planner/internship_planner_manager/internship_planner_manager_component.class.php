<?php

abstract class InternshipPlannerManagerComponent extends WebApplicationComponent
{

    /**
     * Constructor
     * @param InternshipPlanner $internship_planner The internship_planner which
     * provides this component
     */
    function InternshipPlannerManagerComponent($internship_planner)
    {
        parent :: __construct($internship_planner);
    }

    function get_organisation_application_url()
    {
        return $this->get_parent()->get_organisation_application_url();
    }

    function get_category_application_url()
    {
        return $this->get_parent()->get_category_application_url();
    }

}
?>