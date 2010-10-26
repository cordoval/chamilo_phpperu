<?php
namespace application\weblcms\tool\assessment;

use application\weblcms\ToolComponent;

class AssessmentToolHidePublicationComponent extends AssessmentTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>