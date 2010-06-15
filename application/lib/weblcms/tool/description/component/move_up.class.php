<?php
class DescriptionToolMoveUpComponent extends DescriptionTool
{
    function run()
    {
        $component = ToolComponent :: factory(ToolComponent :: ACTION_MOVE, $this);
        $component->run();
    }

    function get_move_direction()
    {
        return -1;
    }
}
?>