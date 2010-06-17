<?php

class DocumentToolCategoryManagerComponent extends DocumentTool {

    function run()
    {
        
        
        $component = ToolComponent :: factory(ToolComponent :: MANAGE_CATEGORIES_COMPONENT, $this);
        $component->run();
    }

}
?>
