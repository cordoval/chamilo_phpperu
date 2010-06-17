<?php

class SurveyToolBuilderComponent extends SurveyTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: BUILD_COMPLEX_CONTENT_OBJECT_COMPONENT, $this);
        $component->run();
    }
}
?>