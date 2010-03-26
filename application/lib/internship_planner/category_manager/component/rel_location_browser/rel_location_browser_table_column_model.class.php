<?php
/**
 * $Id: category_rel_user_browser_table_column_model.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package categories.lib.category_manager.component.category_rel_user_browser
 */
require_once dirname(__FILE__) . '/../../../category_rel_user_table/default_category_rel_user_table_column_model.class.php';
/**
 * Table column model for the user browser table
 */
class InternshipPlannerCategoryRelLocationBrowserTableColumnModel extends DefaultInternshipPlannerCategoryRelLocationTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function InternshipPlannerCategoryRelLocationBrowserTableColumnModel()
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
