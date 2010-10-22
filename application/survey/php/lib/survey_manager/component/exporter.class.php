<?php
namespace application\survey;

use common\libraries\DelegateComponent;

class SurveyManagerExporterComponent extends SurveyManager implements DelegateComponent
{

 /**
     * Runs this component and displays its output.
     */
    function run()
    {
        SurveyExportManager :: launch($this);
    }

}
?>