<?php

class InternshipOrganizerManagerMentorComponent extends InternshipOrganizerManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $mentor_manager = new InternshipOrganizerMentorManager($this->get_parent());
        $mentor_manager->run();
    }
}
?>