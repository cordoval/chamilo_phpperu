<?php

class StreamingVideoToolToggleVisibilityComponent extends StreamingVideoTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: ACTION_TOGGLE_VISIBILITY, $this);
        $component->run();
    }
}
?>