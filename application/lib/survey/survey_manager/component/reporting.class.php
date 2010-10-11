<?php

class SurveyManagerReportingComponent extends SurveyManager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        SurveyReportingManager :: launch($this);
    }
}
?>