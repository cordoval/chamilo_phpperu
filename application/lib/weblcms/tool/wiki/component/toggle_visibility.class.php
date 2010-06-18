<?php
class WikiToolToggleVisibilityComponent extends WikiTool
{
    function run()
    {
        $toggle_visibility = ToolComponent :: factory(ToolComponent :: ACTION_TOGGLE_VISIBILITY, $this);
        $toggle_visibility->run();
    }
}
?>
