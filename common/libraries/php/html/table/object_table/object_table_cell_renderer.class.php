<?php
/**
 * $Id: object_table_cell_renderer.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html.table.object_table
 */
abstract class ObjectTableCellRenderer
{
    private $column_model;

    function set_column_model($column_model)
    {
        $this->column_model = $column_model;
    }

    function get_column_model()
    {
        return $this->column_model;
    }

    function is_display_order_column()
    {
        return $this->get_column_model()->is_display_order_column();
    }

    abstract function render_cell($column, $object);

    abstract function render_id_cell($object);
}
?>