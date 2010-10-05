<?php
class CalendarToolBrowserComponent extends CalendarTool
{
    const PARAM_FILTER = 'filter';
    const FILTER_TODAY = 'today';
    const FILTER_THIS_WEEK = 'week';
    const FILTER_THIS_MONTH = 'month';

    function run()
    {
        ToolComponent :: launch($this);
    }
}
?>