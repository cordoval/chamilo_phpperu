<?php
namespace application\weblcms\tool\video_conferencing;

use application\weblcms\Tool;
use common\libraries\Request;
use application\weblcms\ToolComponent;

class VideoConferencingToolMoveDownComponent extends VideoConferencingTool
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