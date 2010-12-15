<?php
namespace application\package;

use common\libraries\WebApplication;
use common\libraries\ObjectTable;
use common\libraries\ObjectTableFormActions;
use common\libraries\ObjectTableFormAction;
use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * @package package.package_manager.component.package_language_browser
 */
require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/package_browser/package_browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/package_browser/package_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/package_browser/package_browser_table_cell_renderer.class.php';

/**
 * Table to display a list of package_languages
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'package_browser_table';

    /**
     * Constructor
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new PackageBrowserTableColumnModel();
        $renderer = new PackageBrowserTableCellRenderer($browser);
        $data_provider = new PackageBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();

        if (! $browser instanceof PackageManagerPackageBrowserComponent)
        {
            $actions[] = new ObjectTableFormAction(PackageManager :: PARAM_DELETE_SELECTED_PACKAGE, Translation :: get('RemoveSelected', null, Utilities :: COMMON_LIBRARIES));
        }

        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>