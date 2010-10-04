<?php
class SurveyToolHidePublicationComponent extends SurveyTool
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