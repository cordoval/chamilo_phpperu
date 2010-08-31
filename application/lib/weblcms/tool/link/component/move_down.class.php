<?php
class LinkToolMoveDownComponent extends LinkTool
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