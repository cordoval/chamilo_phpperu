<?php
class CalendarToolViewerComponent extends CalendarTool
{
    function run()
    {
        $viewer = ToolComponent :: factory(ToolComponent :: ACTION_VIEW, $this);
        $viewer->run();
    }
}
?>