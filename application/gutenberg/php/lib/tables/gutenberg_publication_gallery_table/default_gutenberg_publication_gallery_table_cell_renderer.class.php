<?php
abstract class DefaultGutenbergPublicationGalleryTableCellRenderer implements GalleryObjectTableCellRenderer
{

    function DefaultGutenbergPublicationGalleryTableCellRenderer()
    {
    }

    function render_cell($publication)
    {
        $html = array();
        $html[] = $this->get_cell_content($publication);
        return implode("\n", $html);
    }

    function render_id_cell($publication)
    {
        return $publication->get_id();
    }

    abstract function get_cell_content(GutenbergPublication $publication);
}
?>