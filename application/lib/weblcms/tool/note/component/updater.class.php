<?php
class NoteToolUpdaterComponent extends NoteTool
{

    function run()
    {
        $update = ToolComponent :: factory(ToolComponent :: ACTION_UPDATE, $this);
        $update->run();
    }
}
?>
