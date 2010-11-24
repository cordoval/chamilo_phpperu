<?php
namespace application\internship_organizer;

use common\libraries\DelegateComponent;

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