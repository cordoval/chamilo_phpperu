<?php
namespace application\weblcms\tool\glossary;

use application\weblcms\ToolComponent;

class GlossaryToolEvaluateComponent extends GlossaryTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>