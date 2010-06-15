<?php

class DescriptionToolDeleterComponent extends DescriptionTool
{

    function run()
    {
        $deleter = ToolComponent :: factory(ToolComponent :: ACTION_DELETE, $this);
        $deleter->run();
    }

}
?>