<?php

class BlogToolReportingViewerComponent extends BlogTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: VIEW_REPORTING_COMPONENT, $this);
        $component->run();
    }
}
?>