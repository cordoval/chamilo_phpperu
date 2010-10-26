<?php
namespace application\weblcms\tool\document;

use application\weblcms\ToolComponent;

class DocumentToolEvaluateComponent extends DocumentTool
{
    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>