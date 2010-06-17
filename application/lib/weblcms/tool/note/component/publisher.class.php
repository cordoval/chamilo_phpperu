<?php
/**
 * $Id: note_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.note.component
 */
class NoteToolPublisherComponent extends NoteTool
{

    function run()
    {
        $publisher = ToolComponent :: factory(ToolComponent :: ACTION_PUBLISH, $this);
        $publisher->run();
    }
}
?>