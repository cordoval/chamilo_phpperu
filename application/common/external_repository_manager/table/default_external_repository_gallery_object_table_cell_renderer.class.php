<?php
class DefaultExternalRepositoryGalleryObjectTableCellRenderer implements GalleryObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultExternalRepositoryGalleryObjectTableCellRenderer()
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
        $html[] = '<div style="width: 20px; float: right;">';
        $html[] = $this->get_modification_links($object);
        $html[] = '</div>';
        $html[] = $this->get_cell_content($object);
        return implode("\n", $html);
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>