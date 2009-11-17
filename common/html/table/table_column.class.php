<?php
/**
 * $Id: table_column.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html.table
 */
interface TableColumn
{

    /**
     * Gets the title of this column.
     * @return string The title.
     */
    function get_title();

    /**
     * Sets the title of this column.
     * @param string $title The new title.
     */
    function set_title($title);

    /**
     * Determine if the table's contents may be sorted by this column.
     * @return boolean True if sorting by this column is allowed, false
     *                 otherwise.
     */
    function is_sortable();

    function get_name();
}
?>