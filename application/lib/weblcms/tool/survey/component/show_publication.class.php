<?php
class SurveyToolShowPublicationComponent extends SurveyTool
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