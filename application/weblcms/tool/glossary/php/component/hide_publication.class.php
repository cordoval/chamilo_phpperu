<?php
namespace application\weblcms\tool\glossary;

use application\weblcms\ToolComponent;

class GlossaryToolHidePublicationComponent extends GlossaryTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

}
?>