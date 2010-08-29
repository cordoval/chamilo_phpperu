<?php
class CalendarToolHidePublicationComponent extends CalendarTool
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