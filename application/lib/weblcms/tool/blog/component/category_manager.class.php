<?php

class BlogToolCategoryManagerComponent extends BlogTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: MANAGE_CATEGORIES_COMPONENT, $this);
        $component->run();
    }
}
?>