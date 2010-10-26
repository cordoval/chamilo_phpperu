<?php
namespace application\weblcms\tool\link;

use application\weblcms\ToolComponent;

class LinkToolEvaluateComponent extends LinkTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>