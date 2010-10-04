<?php
/**
 * $Id: subscribe_user_browser_table_column_model.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package groups.lib.group_manager.component.subscribe_user_browser
 */
require_once Path :: get_user_path() . 'lib/user_table/default_user_table_column_model.class.php';
/**
 * Table column model for the user browser table
 */
class SubscribeUserBrowserTableColumnModel extends DefaultUserTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function SubscribeUserBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->add_column(new ObjectTableColumn(User :: PROPERTY_USERNAME));
        $this->add_column(new ObjectTableColumn(User :: PROPERTY_EMAIL));
        $this->add_column(new ObjectTableColumn(User :: PROPERTY_STATUS));
        $this->add_column(new ObjectTableColumn(User :: PROPERTY_PLATFORMADMIN));
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