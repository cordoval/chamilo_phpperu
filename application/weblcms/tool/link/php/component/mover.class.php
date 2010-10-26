<?php
namespace application\weblcms\tool\link;

use application\weblcms\ToolComponent;

class LinkToolMoverComponent extends LinkTool
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