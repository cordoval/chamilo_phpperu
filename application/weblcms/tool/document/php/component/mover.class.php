<?php
namespace application\weblcms\tool\document;

use application\weblcms\Tool;
use common\libraries\Request;
use application\weblcms\ToolComponent;

class DocumentToolMoverComponent extends DocumentTool
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