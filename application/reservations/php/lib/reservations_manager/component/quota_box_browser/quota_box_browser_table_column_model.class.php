<?php

namespace application\reservations;

use common\libraries\WebApplication;
use common\libraries\StaticTableColumn;
/**
 * $Id: quota_box_browser_table_column_model.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component.quota_box_browser
 */
require_once WebApplication :: get_application_class_lib_path('reservations') . 'tables/quota_box_table/default_quota_box_table_column_model.class.php';
/**
 * Table column model for the user browser table
 */
class QuotaBoxBrowserTableColumnModel extends DefaultQuotaBoxTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function __construct()
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