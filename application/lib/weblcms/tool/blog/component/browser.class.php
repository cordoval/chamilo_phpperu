<?php
class BlogToolBrowserComponent extends BlogTool
{
    function run()
    {
        $tool_component = ToolComponent :: factory(ToolComponent :: ACTION_BROWSE, $this);
        $tool_component->run();
    }

    function get_publications()
    {
        return $this->get_parent()->get_publications();
    }
}
?>