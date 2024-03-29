<?php

namespace application\reservations;

use common\libraries\ObjectTableCellRenderer;
/**
 * $Id: default_quota_box_table_cell_renderer.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.tables.quota_box_table
 */
/**
 * TODO: Add comment
 */
class DefaultQuotaBoxTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function __construct($browser)
    {
    
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $quota_box)
    {
        if ($property = $column->get_name())
        {
            switch ($property)
            {
                case QuotaBox :: PROPERTY_NAME :
                    return $quota_box->get_name();
                case QuotaBox :: PROPERTY_DESCRIPTION :
                    return strip_tags($quota_box->get_description());
            }
        
        }
        
        return '&nbsp;';
    }

    function render_id_cell($quota_box)
    {
        return $quota_box->get_id();
    }
}
?>