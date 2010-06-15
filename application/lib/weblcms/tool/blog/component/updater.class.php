<?php

class BlogToolUpdaterComponent extends BlogTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: ACTION_UPDATE, $this);
        $component->run();
    }
}
?>