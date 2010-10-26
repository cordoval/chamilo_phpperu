<?php
namespace application\weblcms\tool\survey;

use application\weblcms\ToolComponent;

class SurveyToolEvaluateComponent extends SurveyTool
{
    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>