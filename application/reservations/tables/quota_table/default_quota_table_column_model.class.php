<?php
/**
 * $Id: default_quota_table_column_model.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.tables.quota_table
 */
require_once dirname(__FILE__) . '/../../quota.class.php';

/**
 * TODO: Add comment
 */
class DefaultQuotaTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultQuotaTableColumnModel()
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
        $columns[] = new ObjectTableColumn(Quota :: PROPERTY_CREDITS, true);
        $columns[] = new ObjectTableColumn(Quota :: PROPERTY_TIME_UNIT, true);
        return $columns;
    }
}
?>