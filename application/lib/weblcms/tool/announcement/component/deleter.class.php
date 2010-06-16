<?php

class AnnouncementToolDeleterComponent extends AnnouncementTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: ACTION_DELETE, $this);
        $component->run();
    }
}
?>