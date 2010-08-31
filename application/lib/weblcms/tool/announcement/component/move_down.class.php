<?php
class AnnouncementToolMoveDownComponent extends AnnouncementTool
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