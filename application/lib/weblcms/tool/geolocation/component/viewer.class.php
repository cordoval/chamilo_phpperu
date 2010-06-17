<?php
class GeolocationToolViewerComponent extends AnnouncementTool
{
    function run()
    {
        $viewer = ToolComponent :: factory(ToolComponent :: ACTION_VIEW, $this);
        $viewer->run();
    }
}
?>