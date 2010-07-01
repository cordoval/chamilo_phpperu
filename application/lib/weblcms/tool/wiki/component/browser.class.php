<?php

class WikiToolBrowserComponent extends WikiTool
{

    function run()
    {
        $tool_component = ToolComponent :: factory(ToolComponent :: ACTION_BROWSE, $this);
        $tool_component->run();
    }

}
?>