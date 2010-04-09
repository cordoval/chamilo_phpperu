<?php
/**
 * $Id: subscribe_group_browser_table_column_model.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component.subscribe_group_browser
 */
require_once Path :: get_group_path() . '/lib/group_table/default_group_table_column_model.class.php';
/**
 * Table column model for the user browser table
 */
class SubscribeGroupBrowserTableColumnModel extends DefaultGroupTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function SubscribeGroupBrowserTableColumnModel()
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