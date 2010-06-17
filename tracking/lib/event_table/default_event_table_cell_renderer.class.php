<?php
/**
 * $Id: default_event_table_cell_renderer.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.lib.event_table
 */

/**
 * TODO: Add comment
 */
class DefaultEventTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultEventTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $event)
    {
        if ($property = $column->get_property())
        {
            switch ($property)
            {
                case Event :: PROPERTY_NAME :
                    return $event->get_name();
                case Event :: PROPERTY_BLOCK :
                    return $event->get_block();
            }
        }
        return '&nbsp;';
    }

    function render_id_cell($event)
    {
        return $event->get_id();
    }
}
?>