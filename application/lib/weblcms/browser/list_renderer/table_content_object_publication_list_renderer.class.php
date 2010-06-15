<?php
/**
 * $Id: table_content_object_publication_list_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.browser.list_renderer
 */
require_once dirname(__FILE__) . '/../content_object_publication_list_renderer.class.php';
require_once dirname(__FILE__) . '/../object_publication_table/object_publication_table.class.php';
/**
 * Renderer to display a sortable table with learning object publications.
 */
class TableContentObjectPublicationListRenderer extends ContentObjectPublicationListRenderer
{
    /**
     * Returns the HTML output of this renderer.
     * @return string The HTML output
     */
    function as_html()
    {
        $table = new ObjectPublicationTable($this, $this->get_user(), $this->get_allowed_types(), $this->get_search_condition(), $this->get_content_object_publication_list_cell_renderer($this->get_browser()), $this->get_content_object_publication_list_column_model());
        return $table->as_html();
    }

    function get_content_object_publication_list_cell_renderer($browser)
    {
        return $this->get_browser()->get_parent()->get_content_object_publication_list_cell_renderer($browser);
    }

    function get_content_object_publication_list_column_model()
    {
        return $this->get_browser()->get_parent()->get_content_object_publication_list_column_model();
    }
}
?>