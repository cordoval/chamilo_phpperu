<?php
class WikiToolMoveUpComponent extends WikiTool
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
