<?php

require_once dirname(__FILE__) . '/browser/wiki_publication_cell_renderer.class.php';

class WikiToolBrowserComponent extends WikiTool
{

    function run()
    {
        $tool_component = ToolComponent :: factory(ToolComponent :: ACTION_BROWSE, $this);
        $tool_component->run();
    }

	function get_content_object_publication_table_cell_renderer($tool_browser)
    {
        return new WikiPublicationCellRenderer($tool_browser);
    }

}
?>