<?php
namespace application\weblcms\tool\survey;

use application\weblcms\Tool;
use common\libraries\Request;
use application\weblcms\ToolComponent;

class SurveyToolMoverComponent extends SurveyTool
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