<?php

class AssessmentToolBuilderComponent extends AssessmentTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: BUILD_COMPLEX_CONTENT_OBJECT_COMPONENT, $this);
        $component->run();
    }
}
?>