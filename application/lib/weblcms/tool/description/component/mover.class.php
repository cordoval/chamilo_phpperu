<?php

class DescriptionToolMoverComponent extends DescriptionTool
{

    function run()
    {
        $mover = ToolComponent :: factory(ToolComponent :: ACTION_BROWSE, $this);
        $mover->run();
    }

}
?>