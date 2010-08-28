<?php

class InternshipOrganizerManagerRegionComponent extends InternshipOrganizerManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        InternshipOrganizerRegionManager :: launch($this);
    }
}
?>