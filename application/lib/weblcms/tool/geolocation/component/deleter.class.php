<?php

class GeolocationToolDeleterComponent extends GeolocationTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: ACTION_DELETE, $this);
        $component->run();
    }
}
?>