<?php

class LinkToolUpdaterComponent extends LinkTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: ACTION_UPDATE, $this);
        $component->run();
    }
}
?>