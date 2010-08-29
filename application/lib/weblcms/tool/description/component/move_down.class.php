<?php
class DescriptionToolMoveDownComponent extends DescriptionTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function get_move_direction()
    {
        return 1;
    }
}
?>