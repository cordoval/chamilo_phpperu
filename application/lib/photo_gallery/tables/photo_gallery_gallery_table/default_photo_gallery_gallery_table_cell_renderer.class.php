<?php
abstract class DefaultPhotoGalleryGalleryTableCellRenderer implements GalleryObjectTableCellRenderer
{

    function DefaultPhotoGalleryGalleryTableCellRenderer()
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

    abstract function get_cell_content(PhotoGallery $publication);
}
?>