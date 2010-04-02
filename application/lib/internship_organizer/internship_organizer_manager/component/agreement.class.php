<?php

class InternshipOrganizerManagerAgreementComponent extends InternshipOrganizerManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $agreement_manager = new InternshipOrganizerAgreementManager($this->get_parent());
        $agreement_manager->run();
    }
}
?>