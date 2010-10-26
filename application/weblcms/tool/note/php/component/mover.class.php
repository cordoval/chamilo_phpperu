<?php
namespace application\weblcms\tool\note;

class NoteToolMoverComponent extends NoteTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function get_move_direction()
    {
        return Request::get(Tool::PARAM_MOVE_DIRECTION);
    }
}
?>
