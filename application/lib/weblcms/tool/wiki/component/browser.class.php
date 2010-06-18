<?php
class WikiToolBrowserComponent extends WikiTool
{
    const PARAM_FILTER = 'filter';
    const FILTER_TODAY = 'today';
    const FILTER_THIS_WEEK = 'week';
    const FILTER_THIS_MONTH = 'month';

    function run()
    {
        $tool_component = ToolComponent :: factory(ToolComponent :: ACTION_BROWSE, $this);
        $tool_component->run();
    }


}
?>