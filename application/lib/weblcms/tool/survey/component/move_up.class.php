<?php
class SurveyToolMoveUpComponent extends SurveyTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function get_move_direction()
    {
        return - 1;
    }
}
?>