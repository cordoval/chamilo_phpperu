<?php
class DocumentToolHidePublicationComponent extends DocumentTool
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