<?php
/**
 * $Id: default_gutenberg_publication_table_cell_renderer.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.gutenberg.tables.gutenberg_publication_table
 */

require_once WebApplication :: get_application_class_lib_path('gutenberg') . 'gutenberg_publication.class.php';

class DefaultGutenbergPublicationTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultGutenbergPublicationTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param GutenbergTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $gutenberg_publication The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $gutenberg_publication)
    {
        $content_object = $gutenberg_publication->get_publication_object();
        
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
                return $content_object->get_title();
            case ContentObject :: PROPERTY_DESCRIPTION :
                return Utilities :: truncate_string($content_object->get_description(), 200);
            default :
                return '&nbsp;';
        }
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>