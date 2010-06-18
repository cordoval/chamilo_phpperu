<?php
class LearningPathToolToggleVisibilityComponent extends LearningPathTool
{
    function run()
    {
        $toggle_visibility = ToolComponent :: factory(ToolComponent :: ACTION_TOGGLE_VISIBILITY, $this);
        $toggle_visibility->run();
    }
}
?>
