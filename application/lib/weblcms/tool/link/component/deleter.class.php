<?php

class LinkToolDeleterComponent extends LinkTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: ACTION_DELETE, $this);
        $component->run();
    }
}
?>