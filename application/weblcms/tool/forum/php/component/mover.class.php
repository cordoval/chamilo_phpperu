<?php
namespace application\weblcms\tool\forum;

use common\libraries\Request;
use application\weblcms\ToolComponent;

class ForumToolMoverComponent extends ForumTool
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
