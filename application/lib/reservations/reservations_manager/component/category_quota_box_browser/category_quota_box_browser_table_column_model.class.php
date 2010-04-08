<?php
/**
 * $Id: category_quota_box_browser_table_column_model.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component.category_quota_box_browser
 */
require_once dirname(__FILE__) . '/../../../tables/category_quota_box_table/default_category_quota_box_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../quota_box.class.php';
require_once dirname(__FILE__) . '/../../../quota_box_rel_category.class.php';
/**
 * Table column model for the user browser table
 */
class CategoryQuotaBoxBrowserTableColumnModel extends DefaultCategoryQuotaBoxTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function CategoryQuotaBoxBrowserTableColumnModel()
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