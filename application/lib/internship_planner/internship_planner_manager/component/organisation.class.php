<?php

class InternshipPlannerManagerOrganisationComponent extends InternshipPlannerManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $organisation_manager = new InternshipPlannerOrganisationManager($this->get_parent());
        $organisation_manager->run();
    }
}
?>