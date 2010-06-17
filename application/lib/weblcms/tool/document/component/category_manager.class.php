<?php

class DocumentToolCategoryManagerComponent extends DocumentTool {

    function run()
    {
        $component = ToolComponent :: factory(ToolComponent :: ACTION_MANAGE_CATEGORIES, $this);
        $component->run();
    }

}
?>
