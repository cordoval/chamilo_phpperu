<?php

class InternshipOrganizerManagerOrganisationComponent extends InternshipOrganizerManager
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