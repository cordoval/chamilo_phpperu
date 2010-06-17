<?php

class StreamingVideoToolDeleterComponent extends StreamingVideoTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: ACTION_DELETE, $this);
        $component->run();
    }
}
?>