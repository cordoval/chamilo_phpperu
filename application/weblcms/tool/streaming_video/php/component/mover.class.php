<?php
namespace application\weblcms\tool\streaming_video;

class StreamingVideoToolMoveDownComponent extends StreamingVideoTool
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