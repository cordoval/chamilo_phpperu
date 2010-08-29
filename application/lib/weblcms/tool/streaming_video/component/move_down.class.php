<?php
class StreamingVideoToolMoveDownComponent extends StreamingVideoTool
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