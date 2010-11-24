<?php
namespace application\weblcms\tool\blog;

use application\weblcms\Tool;
use common\libraries\Request;
use application\weblcms\ToolComponent;

class BlogToolMoverComponent extends BlogTool
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