<?php
/**
 * $Id: remote_package_browser_table.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_manager.component.remote_package_browser
 */
require_once Path :: get_admin_path() . 'lib/package_manager/component/remote_package_browser/remote_package_browser_table_data_provider.class.php';
require_once Path :: get_admin_path() . 'lib/package_manager/component/remote_package_browser/remote_package_browser_table_column_model.class.php';
require_once Path :: get_admin_path() . 'lib/package_manager/component/remote_package_browser/remote_package_browser_table_cell_renderer.class.php';

/**
 * Table to display a set of learning objects.
 */
class RemotePackageBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'remote_package_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function RemotePackageBrowserTable($browser, $parameters, $condition)
    {
        $model = new RemotePackageBrowserTableColumnModel();
        $renderer = new RemotePackageBrowserTableCellRenderer($browser);
        $data_provider = new RemotePackageBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, RemotePackageBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(PackageManager :: PARAM_INSTALL_SELECTED, Translation :: get('InstallSelected'), false);
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>