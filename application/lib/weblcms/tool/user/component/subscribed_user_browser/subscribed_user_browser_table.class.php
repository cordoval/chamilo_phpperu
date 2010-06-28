<?php
/**
 * $Id: subscribed_user_browser_table.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component.subscribe_user_browser
 */
require_once dirname(__FILE__) . '/subscribed_user_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/subscribed_user_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/subscribed_user_browser_table_cell_renderer.class.php';
/**
 * Table to display a list of users not subscribed to a course.
 */
class SubscribedUserBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'subscribed_user_browser_table';

    /**
     * Constructor
     */
    function SubscribedUserBrowserTable($browser, $parameters, $condition)
    {
        $model = new SubscribedUserBrowserTableColumnModel();
        $renderer = new SubscribedUserBrowserTableCellRenderer($browser);
        $data_provider = new SubscribedUserBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, Utilities :: camelcase_to_underscores(__CLASS__), $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = new ObjectTableFormActions(Tool :: PARAM_ACTION);

        $group_id = Request :: get(WeblcmsManager :: PARAM_GROUP);

        if (!isset($group_id ))
        {
            if (Request :: get(WeblcmsManager :: PARAM_TOOL_ACTION) != UserTool :: ACTION_SUBSCRIBE_USER_BROWSER)
            {
                $actions->add_form_action(new ObjectTableFormAction(UserTool :: ACTION_UNSUBSCRIBE, Translation :: get('UnsubscribeSelected'), false));
            }
            else
            {
                $actions->add_form_action(new ObjectTableFormAction(UserTool :: ACTION_SUBSCRIBE, Translation :: get('SubscribeSelectedAsStudent'), false));
                $actions->add_form_action(new ObjectTableFormAction(UserTool :: ACTION_SUBSCRIBE_AS_ADMIN, Translation :: get('SubscribeSelectedAsAdmin'), false));
            }
        }

        if ($browser->get_course()->is_course_admin($browser->get_user()))
        {
            $this->set_form_actions($actions);
        }
        $this->set_default_row_count(20);
    }
    
    function handle_table_action()
    {
    	$ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
    	Request :: set_get(UserTool :: PARAM_USERS, $ids);
    }
}
?>