<?php

class UserToolIntroductionPublisherComponent extends UserTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: INTRODUCTION_PUBLISHER_COMPONENT, $this);
        $component->run();
    }
}
?>