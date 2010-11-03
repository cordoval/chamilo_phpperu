<?php
namespace application\internship_organizer;

use common\libraries\DelegateComponent;

class InternshipOrganizerManagerOrganisationComponent extends InternshipOrganizerManager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        InternshipOrganizerOrganisationManager :: launch($this);
    }
}
?>