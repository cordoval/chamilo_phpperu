<?php

class ForumToolDeleterComponent extends ForumTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: ACTION_DELETE, $this);
        $component->run();
    }
}
?>