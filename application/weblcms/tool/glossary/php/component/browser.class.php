<?php
namespace application\weblcms\tool\glossary;

use application\weblcms\ToolComponent;

class GlossaryToolBrowserComponent extends GlossaryTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>