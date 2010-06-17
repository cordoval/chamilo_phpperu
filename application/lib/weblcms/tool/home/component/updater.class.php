<?php

class HomeToolUpdaterComponent extends HomeTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: ACTION_UPDATE, $this);
        $component->run();
    }
}
?>