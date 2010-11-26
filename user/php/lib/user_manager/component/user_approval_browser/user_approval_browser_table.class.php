<?php
namespace user;

use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\ObjectTable;
use common\libraries\ObjectTableFormActions;
use common\libraries\ObjectTableFormAction;

/**
 * $Id: user_approval_browser_table.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component.user_approval_browser
 */
require_once dirname(__FILE__) . '/user_approval_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/user_approval_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/user_approval_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of users.
 */
class UserApprovalBrowserTable extends ObjectTable
{
    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new UserApprovalBrowserTableColumnModel();
        $renderer = new UserApprovalBrowserTableCellRenderer($browser);
        $data_provider = new UserApprovalBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, Utilities :: get_classname_from_namespace(__CLASS__, true), $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = new ObjectTableFormActions(__NAMESPACE__);
        //Deactivated: What should happen when a user is removed ? Full remove or deactivation of account ?
        $actions->add_form_action(new ObjectTableFormAction(UserManager :: ACTION_APPROVE_USER, Translation :: get('ApproveSelected'), false));
        $actions->add_form_action(new ObjectTableFormAction(UserManager :: ACTION_DENY_USER, Translation :: get('DenySelected'), false));
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }

	static function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $ids = self :: get_selected_ids($class);
        Request :: set_get(UserManager :: PARAM_USER_USER_ID, $ids);
    }
}
?>