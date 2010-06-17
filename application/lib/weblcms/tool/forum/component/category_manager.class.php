<?php

class ForumToolCategoryManagerComponent extends ForumTool
{
    function run()
    {
    	xdebug_break();
        $component = ToolComponent :: factory(ToolComponent :: MANAGE_CATEGORIES_COMPONENT, $this);
        $component->run();
    }
}
?>