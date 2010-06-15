<?php

class LinkToolCategoryManagerComponent extends LinkTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: MANAGE_CATEGORIES_COMPONENT, $this);
        $component->run();
    }
}
?>