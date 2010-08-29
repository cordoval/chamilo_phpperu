<?php

class SurveyToolRightsEditorComponent extends SurveyTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function get_available_rights()
    {
        return WeblcmsRights :: get_available_rights();
    }
}
?>