<?php
namespace application\weblcms\tool\assessment;

use application\weblcms\ToolComponent;

class AssessmentToolToggleVisibilityComponent extends AssessmentTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>