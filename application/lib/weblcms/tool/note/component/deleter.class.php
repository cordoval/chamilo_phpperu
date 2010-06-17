<?php
class NoteToolDeleterComponent extends NoteTool
{
    function run()
    {
        $deleter = ToolComponent :: factory(ToolComponent :: ACTION_DELETE, $this);
        $deleter->run();
    }
}
?>
