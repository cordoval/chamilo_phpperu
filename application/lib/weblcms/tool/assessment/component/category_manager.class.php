<?php

class AssessmentToolCategoryManagerComponent extends AssessmentTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: MANAGE_CATEGORIES_COMPONENT, $this);
        $component->run();
    }
}
?>