<?php

class InternshipOrganizerManagerRegionComponent extends InternshipOrganizerManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $region_manager = new InternshipOrganizerRegionManager($this->get_parent());
        $region_manager->run();
    }
}
?>