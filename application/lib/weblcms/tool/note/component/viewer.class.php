<?php
/**
 * $Id: note_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.note.component
 */

class NoteToolViewerComponent extends NoteTool
{
    private $action_bar;
    private $introduction_text;

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>