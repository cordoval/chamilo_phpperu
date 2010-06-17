<?php

class DocumentToolToggleVisibilityComponent extends DocumentTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: ACTION_TOGGLE_VISIBILITY, $this);
        $component->run();
    }
}
?>