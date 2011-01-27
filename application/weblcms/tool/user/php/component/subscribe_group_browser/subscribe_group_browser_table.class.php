<?php
namespace application\weblcms\tool\user;

use application\weblcms\Tool;
use common\libraries\Utilities;
use common\libraries\Request;
use common\libraries\ObjectTable;
use common\libraries\Translation;
use common\libraries\ObjectTableFormActions;
use common\libraries\ObjectTableFormAction;

/**
 * $Id: subscribe_group_browser_table.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component.subscribe_group_browser
 */
require_once dirname(__FILE__) . '/subscribe_group_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/subscribe_group_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/subscribe_group_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class SubscribeGroupBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'subscribe_group_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new SubscribeGroupBrowserTableColumnModel();
        $renderer = new SubscribeGroupBrowserTableCellRenderer($browser);
        $data_provider = new SubscribeGroupBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, Utilities :: get_classname_from_namespace(__CLASS__, true), $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = new ObjectTableFormActions(__NAMESPACE__, Tool :: PARAM_ACTION);

        $actions->add_form_action(new ObjectTableFormAction(UserTool :: ACTION_SUBSCRIBE_GROUPS, Translation :: get('SubscribeSelected'), false));

        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }

    function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $ids = self :: get_selected_ids($class);
        Request :: set_get(UserTool :: PARAM_GROUPS, $ids);
    }
}
?>