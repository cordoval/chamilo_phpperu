<?php

class GeolocationToolUpdaterComponent extends GeolocationTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: ACTION_UPDATE, $this);
        $component->run();
    }
}
?>