<?php
class WikiToolViewerComponent extends WikiTool
{
    function run()
    {
        $viewer = ToolComponent :: factory(ToolComponent :: ACTION_VIEW, $this);
        $viewer->run();
    }
}
?>