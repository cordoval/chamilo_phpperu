<?php

class DocumentToolCategoryMovercomponent extends DocumentTool{

    function run()
    {
        $component = ToolComponent :: factory(ToolComponent :: ACTION_MOVE_TO_CATEGORY, $this);
        $component->run();
    }
}
?>
