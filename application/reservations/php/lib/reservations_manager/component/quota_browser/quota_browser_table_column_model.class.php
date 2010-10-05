<?php
/**
 * $Id: quota_browser_table_column_model.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component.quota_browser
 */
require_once dirname(__FILE__) . '/../../../tables/quota_table/default_quota_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../quota.class.php';
/**
 * Table column model for the user browser table
 */
class QuotaBrowserTableColumnModel extends DefaultQuotaTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function QuotaBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->set_default_order_column(1);
        $this->add_column(self :: get_modification_column());
    }

    /**
     * Gets the modification column
     * @return ContentObjectTableColumn
     */
    static function get_modification_column()
    {
        if (! isset(self :: $modification_column))
        {
            self :: $modification_column = new StaticTableColumn('');
        }
        return self :: $modification_column;
    }
}
?>