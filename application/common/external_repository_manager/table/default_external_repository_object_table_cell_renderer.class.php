<?php
class DefaultExternalRepositoryObjectTableCellRenderer implements GalleryObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultExternalRepositoryObjectTableCellRenderer()
    {
    }
    
    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($object)
    {
        $html = array();
        
        $html[] = '<h3>' . $object->get_title() . ' (' . Utilities :: format_seconds_to_minutes($object->get_duration()) .')</h3>';
        $html[] = '<img src="' . $object->get_thumbnail() . '"/><br/>';
        $html[] = '<i>' . Utilities ::truncate_string($object->get_description(), 100) . '</i><br/>';
        
        return implode("\n", $html);
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>