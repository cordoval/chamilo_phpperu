<?php

class GlossaryToolPublisherComponent extends GlossaryTool
{
    function run()
    {
    	$component = ToolComponent :: factory(ToolComponent :: ACTION_PUBLISH, $this);
        $component->run();
    }
}
?>