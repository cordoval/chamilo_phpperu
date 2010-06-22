<?php

class CalendarToolReportingViewerComponent extends CalendarTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: VIEW_REPORTING_COMPONENT, $this);
        $component->run();
    }
}
?>