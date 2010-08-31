<?php
class NoteToolHidePublicationComponent extends NoteTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function get_hidden()
    {
        return 1;
    }
}
?>