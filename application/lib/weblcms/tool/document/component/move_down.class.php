<?php
class DocumentToolMoveDownComponent extends DocumentTool
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