<?php
class AssessmentToolShowPublicationComponent extends AssessmentTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function get_hidden()
    {
        return 0;
    }
}
?>