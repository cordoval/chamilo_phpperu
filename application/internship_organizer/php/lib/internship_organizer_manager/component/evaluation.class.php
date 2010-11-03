<?php
namespace application\internship_organizer;


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