<?php
namespace application\weblcms\tool\survey;

use application\weblcms\ToolComponent;

class SurveyToolUpdaterComponent extends SurveyTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>