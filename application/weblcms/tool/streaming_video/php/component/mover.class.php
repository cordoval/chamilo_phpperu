<?php
namespace application\weblcms\tool\streaming_video;

use application\weblcms\Tool;
use common\libraries\Request;
use application\weblcms\ToolComponent;

class StreamingVideoToolMoveDownComponent extends StreamingVideoTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function get_move_direction()
    {
        return Request :: get(Tool :: PARAM_MOVE_DIRECTION);
    }
}
?>