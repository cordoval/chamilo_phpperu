<?php
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