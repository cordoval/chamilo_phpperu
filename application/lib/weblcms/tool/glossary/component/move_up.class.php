<?php
class GlossaryToolMoveUpComponent extends GlossaryTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function get_move_direction()
    {
        return - 1;
    }
}
?>
