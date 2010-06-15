<?php

class DescriptionToolBrowserComponent extends DescriptionTool
{

    function run()
    {
        $component = ToolComponent :: factory(ToolComponent :: ACTION_BROWSE, $this);
        $component->run();
    }

}
?>