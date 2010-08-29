<?php
class NoteToolMoveUpComponent extends NoteTool
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
