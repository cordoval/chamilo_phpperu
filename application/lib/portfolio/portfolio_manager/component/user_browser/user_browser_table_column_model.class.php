<?php
/**
 * $Id: user_browser_table_column_model.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio.portfolio_manager.component.user_browser
 */
require_once Path :: get_user_path() . 'lib/user_table/default_user_table_column_model.class.php';
/**
 * Table column model for the user browser table
 */
class UserBrowserTableColumnModel extends DefaultUserTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function UserBrowserTableColumnModel()
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