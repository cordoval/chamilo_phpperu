<?php

class DefaultPhotoGalleryTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultPhotoGalleryTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param PhotoGalleryTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $photo_gallery The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $photo_gallery)
    {
        $content_object = $photo_gallery->get_publication_object();
        
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