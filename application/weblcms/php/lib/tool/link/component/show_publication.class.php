<?php
class LinkToolShowPublicationComponent extends LinkTool
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