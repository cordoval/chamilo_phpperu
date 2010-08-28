<?php

class InternshipOrganizerManagerAgreementComponent extends InternshipOrganizerManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        InternshipOrganizerAgreementManager :: launch($this);
    }
}
?>