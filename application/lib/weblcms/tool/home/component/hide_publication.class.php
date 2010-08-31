<?php
class HomeToolHidePublicationComponent extends HomeTool
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