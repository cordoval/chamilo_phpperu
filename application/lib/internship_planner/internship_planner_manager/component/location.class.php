<?php

class InternshipPlannerManagerLocationComponent extends InternshipPlannerManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $location_manager = new InternshipLocationManager($this->get_parent());
        $location_manager->run();
    }
}
?>