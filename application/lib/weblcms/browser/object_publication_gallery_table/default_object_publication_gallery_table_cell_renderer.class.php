<?php
class DefaultObjectPublicationGalleryTableCellRenderer implements GalleryObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultObjectPublicationGalleryTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($publication)
    {
        $object = $publication->get_object();
        $html = array();

        $html[] = '<h3>' . $object->get_title() .'</h3>';
//        $html[] = '<img src="' . $object->get_thumbnail() . '"/><br/>';
        $html[] = '<i>' . Utilities ::truncate_string($object->get_description(), 100) . '</i><br/>';

        return implode("\n", $html);
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>