<?php
/**
 * $Id: default_laika_calculated_result_table_cell_renderer.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.tables.laika_calculated_result_table
 */

require_once dirname(__FILE__) . '/../../laika_calculated_result.class.php';
/**
 * TODO: Add comment
 */
class DefaultLaikaCalculatedResultTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultLaikaCalculatedResultTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $laika_calculated_result)
    {
        switch ($column->get_name())
        {
            case User :: PROPERTY_LASTNAME :
                return $laika_calculated_result->get_user()->get_lastname();
            case User :: PROPERTY_FIRSTNAME :
                return $laika_calculated_result->get_user()->get_firstname();
            case User :: PROPERTY_EMAIL :
                return $laika_calculated_result->get_user()->get_email();
            case LaikaAttempt :: PROPERTY_DATE :
                return date('Y-m-d, H:i', $laika_calculated_result->get_attempt()->get_date());
            default :
                return '&nbsp;';
        }
    }

    function render_id_cell($laika_calculated_result)
    {
        return $laika_calculated_result->get_user()->get_id();
    }
}
?>