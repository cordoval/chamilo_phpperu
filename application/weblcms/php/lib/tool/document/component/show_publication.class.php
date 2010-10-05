<?php
class DocumentToolShowPublicationComponent extends DocumentTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function get_hidden()
    {
        return 0;
    }
}
?>