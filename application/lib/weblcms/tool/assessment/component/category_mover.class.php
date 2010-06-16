<?php

class AssessmentToolCategoryMoverComponent extends AssessmentTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: MOVE_TO_CATEGORY_COMPONENT, $this);
        $component->run();
    }
}
?>