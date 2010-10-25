<?php
namespace application\weblcms\tool\learning_path;

class LearningPathToolMoverComponent extends LearningPathTool
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
