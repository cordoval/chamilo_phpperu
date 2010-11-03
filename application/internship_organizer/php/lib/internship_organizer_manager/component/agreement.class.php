<?php
namespace application\internship_organizer;


class InternshipOrganizerManagerAgreementComponent extends InternshipOrganizerManager implements DelegateComponent
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