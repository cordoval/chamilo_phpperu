<?php
class AssessmentToolMoveUpComponent extends AssessmentTool
{
    function run()
    {
        $tool_component = ToolComponent :: factory(ToolComponent :: ACTION_MOVE, $this);
        $tool_component->run();
    }

    function get_move_direction()
    {
        return -1;
    }
}
?>