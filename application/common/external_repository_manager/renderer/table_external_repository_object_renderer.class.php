<?php
require_once dirname(__FILE__) . '/../external_repository_object_renderer.class.php';
require_once dirname(__FILE__) . '/../component/external_repository_browser_table/external_repository_browser_table.class.php';

class TableExternalRepositoryObjectRenderer extends ExternalRepositoryObjectRenderer
{

    /**
     * Returns the HTML output of this renderer.
     * @return string The HTML output
     */
    function as_html()
    {
//        if (method_exists($this->get_tool_browser()->get_parent(), 'get_content_object_publication_table_cell_renderer'))
//        {
//            $object_publication_table_cell_renderer = $this->get_tool_browser()->get_parent()->get_content_object_publication_table_cell_renderer($this);
//        }
//        else
//        {
            $object_publication_table_cell_renderer = null;
//        }

//        if (method_exists($this->get_tool_browser()->get_parent(), 'get_content_object_publication_table_column_model'))
//        {
//            $object_publication_table_column_model = $this->get_tool_browser()->get_parent()->get_content_object_publication_table_column_model();
//        }
//        else
//        {
            $object_publication_table_column_model = null;
//        }

        $table = new ExternalRepositoryBrowserTable($this, $this->get_parameters(), $this->get_condition());
        return $table->as_html();
    }
}
?>