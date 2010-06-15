<?php

class DescriptionToolDeleterComponent extends DescriptionTool
{

    function run()
    {
        $component = ToolComponent :: factory(ToolComponent :: ACTION_DELETE, $this);
        $component->run();
    }

}
?>