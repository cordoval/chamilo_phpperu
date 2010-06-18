<?php
class StreamingVideoToolViewerComponent extends StreamingVideoTool
{
    function run()
    {
        $viewer = ToolComponent :: factory(ToolComponent :: ACTION_VIEW, $this);
        $viewer->run();
    }
}
?>