<?php

class DescriptionToolUpdaterComponent extends DescriptionTool
{

    function run()
    {
        $updater = ToolComponent :: factory(ToolComponent :: ACTION_UPDATE, $this);
        $updater->run();
    }

}
?>