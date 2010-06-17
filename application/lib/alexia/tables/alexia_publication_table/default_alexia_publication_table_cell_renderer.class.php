<?php
/**
 * $Id: default_alexia_publication_table_cell_renderer.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.alexia.tables.alexia_publication_table
 */

require_once dirname(__FILE__) . '/../../alexia_publication.class.php';

class DefaultAlexiaPublicationTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultAlexiaPublicationTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param AlexiaTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $alexia_publication The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $alexia_publication)
    {
        $content_object = $alexia_publication->get_publication_object();
        
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