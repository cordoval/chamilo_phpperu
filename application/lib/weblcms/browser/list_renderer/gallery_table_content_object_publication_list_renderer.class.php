<?php
/**
 * $Id: table_content_object_publication_list_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.browser.list_renderer
 */
require_once dirname(__FILE__) . '/../content_object_publication_list_renderer.class.php';
require_once dirname(__FILE__) . '/../object_publication_gallery_table/object_publication_gallery_table.class.php';
/**
 * Renderer to display a sortable table with learning object publications.
 */
class GalleryTableContentObjectPublicationListRenderer extends ContentObjectPublicationListRenderer
{

    /**
     * Returns the HTML output of this renderer.
     * @return string The HTML output
     */
    function as_html()
    {
        if (method_exists($this->get_tool_browser()->get_parent(), 'get_content_object_publication_table_cell_renderer'))
        {
            $object_publication_table_cell_renderer = $this->get_tool_browser()->get_parent()->get_content_object_publication_table_cell_renderer($this);
        }
        else
        {
            $object_publication_table_cell_renderer = null;
        }

//        if (method_exists($this->get_tool_browser()->get_parent(), 'get_content_object_publication_table_column_model'))
//        {
//            $object_publication_table_column_model = $this->get_tool_browser()->get_parent()->get_content_object_publication_table_column_model();
//        }
//        else
//        {
//            $object_publication_table_column_model = null;
//        }

        $table = new ObjectPublicationGalleryTable($this, $this->get_user(), $this->get_allowed_types(), $this->get_publication_conditions(), $object_publication_table_cell_renderer);
        return $table->as_html();
    }
}
?>