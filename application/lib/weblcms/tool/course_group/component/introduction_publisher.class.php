<?php

class CourseGroupToolIntroductionPublisherComponent extends CourseGroupTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: INTRODUCTION_PUBLISHER_COMPONENT, $this);
        $component->run();
    }
}
?>