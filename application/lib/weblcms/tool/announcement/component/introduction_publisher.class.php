<?php

class AnnouncementToolIntroductionPublisherComponent extends AnnouncementTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: INTRODUCTION_PUBLISHER_COMPONENT, $this);
        $component->run();
    }
}
?>