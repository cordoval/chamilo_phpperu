<?php

class InternshipOrganizerManagerAgreementComponent extends InternshipOrganizerManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $agreement_manager = new InternshipOrganizerAgreementManager($this);
        $agreement_manager->run();
    }
}
?>