<?php
/**
 * $Id: object_table_cell_renderer.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html.table.object_table
 */
/**
 * 
 * TODO: Add comment
 * 
 */
interface ObjectTableCellRenderer
{

    /**
     * TODO: Add comment
     */
    function render_cell($column, $object);

    function render_id_cell($object);
}
?>