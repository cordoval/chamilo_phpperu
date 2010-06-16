<?php

class AnnouncementToolReportingViewerComponent extends AnnouncementTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: VIEW_REPORTING_COMPONENT, $this);
        $component->run();
    }
}
?>