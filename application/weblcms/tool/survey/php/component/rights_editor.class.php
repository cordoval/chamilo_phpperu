<?php
namespace application\weblcms\tool\survey;

use application\weblcms\ToolComponent;

class SurveyToolRightsEditorComponent extends SurveyTool implements DelegateComponent
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