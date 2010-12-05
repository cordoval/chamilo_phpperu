<?php
namespace admin;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\ObjectTable;
use common\libraries\ObjectTableFormAction;
use common\libraries\Utilities;
/**
 * $Id: library_registration_browser_table.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_manager.component.registration_browser
 */
require_once Path :: get_admin_path() . 'lib/package_manager/component/registration_browser/library/library_registration_browser_table_data_provider.class.php';
require_once Path :: get_admin_path() . 'lib/package_manager/component/registration_browser/library/library_registration_browser_table_column_model.class.php';
require_once Path :: get_admin_path() . 'lib/package_manager/component/registration_browser/library/library_registration_browser_table_cell_renderer.class.php';

/**
 * Table to display a set of learning objects.
 */
class LibraryRegistrationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'library_registration_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new LibraryRegistrationBrowserTableColumnModel();
        $renderer = new LibraryRegistrationBrowserTableCellRenderer($browser);
        $data_provider = new LibraryRegistrationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, LibraryRegistrationBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>