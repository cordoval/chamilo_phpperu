<?php
namespace application\weblcms\tool\description;


class DescriptionToolMoveDownComponent extends DescriptionTool
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