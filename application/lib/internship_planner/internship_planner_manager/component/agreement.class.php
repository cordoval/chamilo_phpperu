<?php

class InternshipPlannerManagerOrganisationComponent extends InternshipPlannerManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $agreement_manager = new InternshipPlannerAgreementManager($this->get_parent());
        $agreement_manager->run();
    }
}
?>