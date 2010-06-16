<?php

class AssessmentToolPublisherComponent extends AssessmentTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: ACTION_PUBLISH, $this);
        $component->run();
    }
}
?>