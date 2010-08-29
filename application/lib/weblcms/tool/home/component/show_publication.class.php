<?php
class HomeToolShowPublicationComponent extends HomeTool
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