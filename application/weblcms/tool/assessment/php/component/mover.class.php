<?php
namespace application\weblcms\tool\assessment;

use application\weblcms\ToolComponent;

class AssessmentToolMoverComponent extends AssessmentTool
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