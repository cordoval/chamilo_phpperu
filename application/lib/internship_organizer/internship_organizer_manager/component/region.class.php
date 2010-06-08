<?php

class InternshipOrganizerManagerRegionComponent extends InternshipOrganizerManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $region_manager = new InternshipOrganizerRegionManager($this);
        $region_manager->run();
    }
}
?>