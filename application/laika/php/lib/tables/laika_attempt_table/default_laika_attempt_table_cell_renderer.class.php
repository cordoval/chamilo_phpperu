<?php
namespace application\laika;

use common\libraries\ObjectTableCellRenderer;
/**
 * $Id: default_laika_attempt_table_cell_renderer.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.tables.laika_attempt_table
 */
/**
 * TODO: Add comment
 */
class DefaultLaikaAttemptTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function __construct()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $laika_attempt)
    {
        switch ($column->get_name())
        {
            case LaikaAttempt :: PROPERTY_DATE :
                return date('Y-m-d, H:i', $laika_attempt->get_date());
            default :
                return '&nbsp;';
        }
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>