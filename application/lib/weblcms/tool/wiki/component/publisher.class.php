<?php

class WikiToolPublisherComponent extends WikiTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: ACTION_PUBLISH, $this);
        $component->run();
    }
}
?>