<?php

class InternshipOrganizerManagerPeriodComponent extends InternshipOrganizerManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $period_manager = new InternshipOrganizerPeriodManager($this);
        $period_manager->run();
    }
}
?>