<?php
namespace application\weblcms\tool\link;

use application\weblcms\ToolComponent;

class LinkToolBrowserComponent extends LinkTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function get_publications()
    {
        return $this->get_parent()->get_publications();
    }
}
?>