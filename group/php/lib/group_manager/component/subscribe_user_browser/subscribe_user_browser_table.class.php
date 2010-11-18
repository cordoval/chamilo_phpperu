<?php
namespace group;
use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\ObjectTableFormActions;
use common\libraries\ObjectTableFormAction;
use common\libraries\ObjectTable;

/**
 * $Id: subscribe_user_browser_table.class.php 166 2009-11-12 11:03:06Z vanpouckesven $
 * @package groups.lib.group_manager.component.subscribe_user_browser
 */

require_once dirname(__FILE__) . '/subscribe_user_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/subscribe_user_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/subscribe_user_browser_table_cell_renderer.class.php';
/**
 * Table to display a list of users not subscribed to a course.
 */
class SubscribeUserBrowserTable extends ObjectTable
{
    /**
     * Constructor
     */
    function SubscribeUserBrowserTable($browser, $parameters, $condition)
    {
        $model = new SubscribeUserBrowserTableColumnModel();
        $renderer = new SubscribeUserBrowserTableCellRenderer($browser);
        $data_provider = new SubscribeUserBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, Utilities :: camelcase_to_underscores(__CLASS__), $model, $renderer);
        $this->set_additional_parameters($parameters);

        $actions = new ObjectTableFormActions(__NAMESPACE__);

        $actions->add_form_action(new ObjectTableFormAction(GroupManager :: ACTION_SUBSCRIBE_USER_TO_GROUP, Translation :: get('SubscribeSelected'), false));

        $this->set_form_actions($actions);

        $this->set_default_row_count(20);
    }

	static function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $ids = self :: get_selected_ids($class);
        Request :: set_get(GroupManager :: PARAM_USER_ID, $ids);
    }
}
?>