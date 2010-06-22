<?php

require_once dirname(__FILE__) . '/browser/glossary_publication_cell_renderer.class.php';

class GlossaryToolBrowserComponent extends GlossaryTool
{

    function run()
    {
        $tool_component = ToolComponent :: factory(ToolComponent :: ACTION_BROWSE, $this);
        $tool_component->run();
    }
    
	function get_content_object_publication_table_cell_renderer($tool_browser)
    {
        return new GlossaryPublicationCellRenderer($tool_browser);
    }
}
?>