<?php
class GeolocationToolShowPublicationComponent extends GeolocationTool
{
    function run()
    {
        $tool_component = ToolComponent :: factory(ToolComponent :: ACTION_TOGGLE_VISIBILITY, $this);
        $tool_component->run();
    }

    function get_hidden()
    {
        return 0;
    }
}
?>