<?php

class DocumentToolCategoryMovercomponent extends DocumentTool{

    function run()
    {
        $component = ToolComponent :: factory(ToolComponent :: MOVE_TO_CATEGORY_COMPONENT, $this);
        $component->run();
    }
}
?>
