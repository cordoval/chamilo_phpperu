<?php
namespace application\weblcms\tool\wiki;

use application\weblcms\Tool;
use common\libraries\Request;
use application\weblcms\ToolComponent;

class WikiToolMoverComponent extends WikiTool
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
