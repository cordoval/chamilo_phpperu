<?php
namespace user;
use common\libraries\ObjectTableColumn;
use common\libraries\StaticTableColumn;
/**
 * $Id: admin_user_browser_table_column_model.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component.admin_user_browser
 */
require_once dirname(__FILE__) . '/../../../user_table/default_user_table_column_model.class.php';
/**
 * Table column model for the user browser table
 */
class AdminUserBrowserTableColumnModel extends DefaultUserTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function AdminUserBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->add_column(new ObjectTableColumn(User :: PROPERTY_USERNAME));
        $this->add_column(new ObjectTableColumn(User :: PROPERTY_EMAIL));
        $this->add_column(new ObjectTableColumn(User :: PROPERTY_STATUS));
        $this->add_column(new ObjectTableColumn(User :: PROPERTY_PLATFORMADMIN));
        $this->add_column(new ObjectTableColumn(User :: PROPERTY_ACTIVE));
        //$this->add_column(new ObjectTableColumn(User :: PROPERTY_PLATFORMADMIN));
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