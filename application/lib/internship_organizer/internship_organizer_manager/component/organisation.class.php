<?php

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