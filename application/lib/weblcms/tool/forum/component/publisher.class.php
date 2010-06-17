<?php

class ForumToolPublisherComponent extends ForumTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: ACTION_PUBLISH, $this);
        $component->run();
    }
}
?>