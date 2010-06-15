<?php

class DescriptionToolBrowserComponent extends DescriptionTool
{

    function run()
    {
        $browser = ToolComponent :: factory(ToolComponent :: ACTION_BROWSE, $this);
        $browser->run();
    }

}
?>