<?php
namespace application\weblcms\tool\survey;

class SurveyToolMoverComponent extends SurveyTool
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