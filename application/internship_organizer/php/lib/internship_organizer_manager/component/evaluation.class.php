<?php
namespace application\internship_organizer;

use common\libraries\DelegateComponent;

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