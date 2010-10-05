<?php

class InternshipOrganizerManagerEvaluationComponent extends InternshipOrganizerManager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        InternshipOrganizerEvaluationManager :: launch($this);
    }
}
?>