<?php

class InternshipOrganizerManagerOrganisationComponent extends InternshipOrganizerManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $organisation_manager = new InternshipOrganizerOrganisationManager($this);
        $organisation_manager->run();
    }
}
?>