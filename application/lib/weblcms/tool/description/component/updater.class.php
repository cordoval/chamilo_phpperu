<?php

class DescriptionToolUpdaterComponent extends DescriptionTool
{

    function run()
    {
        $component = ToolComponent :: factory(ToolComponent :: ACTION_UPDATE, $this);
        $component->run();
    }

}
?>