<?php

class AnnouncementToolPublisherComponent extends AnnouncementTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: ACTION_PUBLISH, $this);
        $component->run();
    }
}
?>