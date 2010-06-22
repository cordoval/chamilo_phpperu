<?php
class GlossaryToolViewerComponent extends GlossaryTool
{
    function run()
    {
        $viewer = ToolComponent :: factory(ToolComponent :: ACTION_VIEW, $this);
        $viewer->run();
    }
}
?>