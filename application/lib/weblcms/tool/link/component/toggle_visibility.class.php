<?php

class LinkToolToggleVisibilityComponent extends LinkTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: ACTION_TOGGLE_VISIBILITY, $this);
        $component->run();
    }
}
?>