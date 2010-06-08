<?php

class InternshipOrganizerManagerMentorComponent extends InternshipOrganizerManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $mentor_manager = new InternshipOrganizerMentorManager($this);
        $mentor_manager->run();
    }
}
?>