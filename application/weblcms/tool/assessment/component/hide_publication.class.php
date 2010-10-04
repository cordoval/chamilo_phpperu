<?php
class AssessmentToolHidePublicationComponent extends AssessmentTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function get_hidden()
    {
        return 1;
    }
}
?>