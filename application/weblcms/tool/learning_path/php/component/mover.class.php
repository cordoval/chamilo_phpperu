<?php
namespace application\weblcms\tool\learning_path;

use application\weblcms\Tool;
use common\libraries\Request;
use application\weblcms\ToolComponent;

class LearningPathToolMoverComponent extends LearningPathTool
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
