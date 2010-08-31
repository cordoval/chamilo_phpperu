<?php
class LinkToolHidePublicationComponent extends LinkTool
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