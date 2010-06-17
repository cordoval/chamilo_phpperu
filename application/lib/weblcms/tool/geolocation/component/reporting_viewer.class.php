<?php

class GeolocationToolReportingViewerComponent extends GeolocationTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: VIEW_REPORTING_COMPONENT, $this);
        $component->run();
    }
}
?>