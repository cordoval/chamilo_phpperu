<?php

class ForumToolCategoryManagerComponent extends ForumTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: MANAGE_CATEGORIES_COMPONENT, $this);
        $component->run();
    }
}
?>