<?php

class InternshipOrganizerManagerOrganisationComponent extends InternshipOrganizerManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $organisation_manager = new InternshipOrganizerOrganisationManager($this->get_parent());
        $organisation_manager->run();
    }
}
?>