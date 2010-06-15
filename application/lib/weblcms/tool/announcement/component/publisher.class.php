<?php

class AnnouncementToolPublisherComponent extends AnnouncementTool
{
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('courses announcement tool');
    	$component = ToolComponent :: factory(ToolComponent :: ACTION_PUBLISH, $this);
        $component->run();
    }
}
?>