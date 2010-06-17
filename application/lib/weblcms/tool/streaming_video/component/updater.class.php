<?php

class StreamingVideoToolUpdaterComponent extends StreamingVideoTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: ACTION_UPDATE, $this);
        $component->run();
    }
}
?>