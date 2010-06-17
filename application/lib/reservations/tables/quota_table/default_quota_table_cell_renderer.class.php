<?php
/**
 * $Id: default_quota_table_cell_renderer.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.tables.quota_table
 */

require_once dirname(__FILE__) . '/../../quota.class.php';
/**
 * TODO: Add comment
 */
class DefaultQuotaTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultQuotaTableCellRenderer($browser)
    {
    
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $quota)
    {
        if ($property = $column->get_name())
        {
            switch ($property)
            {
                case Quota :: PROPERTY_ID :
                    return $quota->get_id();
                case Quota :: PROPERTY_CREDITS :
                    return $quota->get_credits();
                case Quota :: PROPERTY_TIME_UNIT :
                    return $quota->get_time_unit() . ' ' . Translation :: get('day(s)');
            }
        
        }
        
        return '&nbsp;';
    }

    function render_id_cell($quota)
    {
        return $quota->get_id();
    }
}
?>