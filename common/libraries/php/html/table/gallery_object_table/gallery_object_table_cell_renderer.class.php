<?php
namespace common\libraries;
/**
 * $Id: gallery_object_table_cell_renderer.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html.table.object_table
 */
/**
 * 
 * TODO: Add comment
 * 
 */
interface GalleryObjectTableCellRenderer
{

    /**
     * TODO: Add comment
     */
    function render_cell($object);

    function render_id_cell($object);
}
?>