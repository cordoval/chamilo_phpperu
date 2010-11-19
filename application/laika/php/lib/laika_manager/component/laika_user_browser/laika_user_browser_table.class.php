<?php
namespace application\laika;

use common\libraries\WebApplication;
use common\libraries\ObjectTable;
/**
 * $Id: laika_user_browser_table.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.laika_manager.component.laika_user_browser
 */
require_once WebApplication :: get_application_class_lib_path('laika') . 'laika_manager/component/laika_user_browser/laika_user_browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('laika') . 'laika_manager/component/laika_user_browser/laika_user_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('laika') . 'laika_manager/component/laika_user_browser/laika_user_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class LaikaUserBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'laika_attempt_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new LaikaUserBrowserTableColumnModel();
        $renderer = new LaikaUserBrowserTableCellRenderer($browser);
        $data_provider = new LaikaUserBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, LaikaUserBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        //		$actions = array();
        //		$actions[GroupManager :: PARAM_REMOVE_SELECTED] = Translation :: get('RemoveSelected');
        //		$actions[GroupManager :: PARAM_TRUNCATE_SELECTED] = Translation :: get('TruncateSelected');
        //		$this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>