<?php

class AssessmentToolDeleterComponent extends AssessmentTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: ACTION_DELETE, $this);
        $component->run();
    }
}
?>