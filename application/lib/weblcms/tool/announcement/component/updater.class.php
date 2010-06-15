<?php

class AnnouncementToolUpdaterComponent extends AnnouncementTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: ACTION_UPDATE, $this);
        $component->run();
    }
}
?>