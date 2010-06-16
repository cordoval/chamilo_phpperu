<?php

class AssessmentToolToggleVisibilityComponent extends AssessmentTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: ACTION_TOGGLE_VISIBILITY, $this);
        $component->run();
    }
}
?>