<?php
namespace application\weblcms\tool\description;

use application\weblcms\Tool;
use common\libraries\Request;
use application\weblcms\ToolComponent;

class DescriptionToolMoverComponent extends DescriptionTool
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