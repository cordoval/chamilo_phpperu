<?php
class StreamingVideoToolViewerComponent extends StreamingVideoTool
{
    function run()
    {
        $viewer = ToolComponent :: factory(ToolComponent :: ACTION_VIEW, $this);
        xdebug_break();
        $viewer->run();
    }
}
?>