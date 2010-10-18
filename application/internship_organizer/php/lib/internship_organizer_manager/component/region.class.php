<?php

class InternshipOrganizerManagerRegionComponent extends InternshipOrganizerManager implements DelegateComponent
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