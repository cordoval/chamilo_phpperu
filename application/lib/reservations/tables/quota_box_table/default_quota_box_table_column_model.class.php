<?php
/**
 * $Id: default_quota_box_table_column_model.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.tables.quota_box_table
 */
require_once dirname(__FILE__) . '/../../quota_box.class.php';

/**
 * TODO: Add comment
 */
class DefaultQuotaBoxTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultQuotaBoxTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(QuotaBox :: PROPERTY_NAME, true);
        $columns[] = new ObjectTableColumn(QuotaBox :: PROPERTY_DESCRIPTION, true);
        return $columns;
    }
}
?>