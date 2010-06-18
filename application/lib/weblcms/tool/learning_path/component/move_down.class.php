<?php
class LearningPathToolMoveDownComponent extends LearningPathTool
{

    function run()
    {
        $mover = ToolComponent :: factory(ToolComponent :: ACTION_MOVE, $this);
        $mover->run();
    }

    function get_move_direction()
    {
        return 1;
    }
}
?>
