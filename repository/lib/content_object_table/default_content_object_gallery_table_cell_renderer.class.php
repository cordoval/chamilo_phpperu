<?php
abstract class DefaultContentObjectGalleryTableCellRenderer implements GalleryObjectTableCellRenderer
{

    function DefaultContentObjectGalleryTableCellRenderer()
    {
    }

    function render_cell($content_object)
    {
        $html = array();
        $html[] = $this->get_cell_content($content_object);
        return implode("\n", $html);
    }

    function render_id_cell($content_object)
    {
        return $content_object->get_id();
    }

    abstract function get_cell_content(ContentObject $content_object);
}
?>