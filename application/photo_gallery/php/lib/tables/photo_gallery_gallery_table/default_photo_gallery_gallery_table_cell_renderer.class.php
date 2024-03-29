<?php
namespace application\photo_gallery;

use common\libraries\GalleryObjectTableCellRenderer;
use application\photo_gallery\PhotoGalleryPublication;

abstract class DefaultPhotoGalleryGalleryTableCellRenderer implements GalleryObjectTableCellRenderer
{

    function __construct()
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

    abstract function get_cell_content(PhotoGalleryPublication $publication);
}
?>