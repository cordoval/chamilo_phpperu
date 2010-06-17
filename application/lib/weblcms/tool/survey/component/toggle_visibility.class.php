<?php

class SurveyToolToggleVisibilityComponent extends SurveyTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: ACTION_TOGGLE_VISIBILITY, $this);
        $component->run();
    }
}
?>