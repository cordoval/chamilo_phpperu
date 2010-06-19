<?php
class GlossaryToolBrowserComponent extends GlossaryTool
{

    function run()
    {
        $tool_component = ToolComponent :: factory(ToolComponent :: ACTION_BROWSE, $this);
        $tool_component->run();
    }
}
?>