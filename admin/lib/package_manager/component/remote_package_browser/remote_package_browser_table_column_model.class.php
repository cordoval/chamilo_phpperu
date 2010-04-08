<?php
/**
 * $Id: remote_package_browser_table_column_model.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.package_manager.component.remote_package_browser
 */
require_once Path :: get_admin_path() . 'lib/tables/remote_package_table/default_remote_package_table_column_model.class.php';
/**
 * Table column model for the user browser table
 */
class RemotePackageBrowserTableColumnModel extends DefaultRemotePackageTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function RemotePackageBrowserTableColumnModel()
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