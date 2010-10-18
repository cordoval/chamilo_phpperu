<?php
class WikiToolShowPublicationComponent extends WikiTool
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