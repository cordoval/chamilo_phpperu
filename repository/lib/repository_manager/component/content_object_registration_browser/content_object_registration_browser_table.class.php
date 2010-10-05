<?php
/**
 * $Id: registration_browser_table.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_manager.component.registration_browser
 */
require_once Path :: get_admin_path() . 'lib/package_manager/component/registration_browser/registration_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/content_object_registration_browser_table_data_provider.class.php';
require_once Path :: get_admin_path() . 'lib/package_manager/component/registration_browser/registration_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/content_object_registration_browser_table_column_model.class.php';
require_once Path :: get_admin_path() . 'lib/package_manager/component/registration_browser/registration_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/content_object_registration_browser_table_cell_renderer.class.php';
require_once Path :: get_admin_path() . 'lib/package_manager/component/registration_browser/registration_browser_table.class.php';

/**
 * Table to display a set of learning objects.
 */
class ContentObjectRegistrationBrowserTable extends RegistrationBrowserTable
{
	function ContentObjectRegistrationBrowserTable($browser, $parameters, $condition)
    {
        $model = new ContentObjectRegistrationBrowserTableColumnModel();
        $renderer = new ContentObjectRegistrationBrowserTableCellRenderer($browser);
        $data_provider = new ContentObjectRegistrationBrowserTableDataProvider($browser, $condition);
        ObjectTable :: __construct($data_provider, Utilities :: camelcase_to_underscores(__CLASS__), $model, $renderer);
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>