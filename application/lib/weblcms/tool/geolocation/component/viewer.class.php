<?php
class GeolocationToolViewerComponent extends GeolocationTool
{
    function run()
    {
        $viewer = ToolComponent :: factory(ToolComponent :: ACTION_VIEW, $this);
        $viewer->run();
    }
}
?>