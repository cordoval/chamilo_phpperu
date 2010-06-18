<?php

class WikiToolUpdaterComponent extends WikiTool

{

    function run()
    {
        $update = ToolComponent :: factory(ToolComponent :: ACTION_UPDATE, $this);
        $update->run();
    }
}
?>
