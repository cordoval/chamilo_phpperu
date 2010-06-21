<?php

class LearningPathToolIntroductionPublisherComponent extends LearningPathTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: INTRODUCTION_PUBLISHER_COMPONENT, $this);
        $component->run();
    }
}
?>