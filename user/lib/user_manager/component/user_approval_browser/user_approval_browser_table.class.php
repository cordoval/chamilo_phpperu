<?php
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
    const DEFAULT_NAME = 'admin_user_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function UserApprovalBrowserTable($browser, $parameters, $condition)
    {
        $model = new UserApprovalBrowserTableColumnModel();
        $renderer = new UserApprovalBrowserTableCellRenderer($browser);
        $data_provider = new UserApprovalBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, UserApprovalBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        //Deactivated: What should happen when a user is removed ? Full remove or deactivation of account ?
        $actions[] =  new ObjectTableFormAction(UserManager :: PARAM_APPROVE_SELECTED, Translation :: get('ApproveSelected'), false);
        $actions[] =  new ObjectTableFormAction(UserManager :: PARAM_DENY_SELECTED, Translation :: get('DenySelected'), false);
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>