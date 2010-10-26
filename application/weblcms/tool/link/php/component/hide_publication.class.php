<?php
namespace application\weblcms\tool\link;

use application\weblcms\ToolComponent;

class LinkToolHidePublicationComponent extends LinkTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>