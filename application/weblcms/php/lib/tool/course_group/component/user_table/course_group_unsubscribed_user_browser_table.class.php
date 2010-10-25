<?php
/**
 * $Id: course_group_unsubscribed_user_browser_table.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group.component.user_table
 */
require_once dirname(__FILE__) . '/course_group_unsubscribed_user_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/course_group_unsubscribed_user_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/course_group_unsubscribed_user_browser_table_cell_renderer.class.php';

class CourseGroupUnsubscribedUserBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'course_group_unsubscribed_user_browser_table';

    /**
     * Constructor
     */
    function CourseGroupUnsubscribedUserBrowserTable($browser, $parameters, $condition)
    {
        $model = new CourseGroupUnsubscribedUserBrowserTableColumnModel();
        $renderer = new CourseGroupUnsubscribedUserBrowserTableCellRenderer($browser);
        $data_provider = new CourseGroupUnsubscribedUserBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, CourseGroupUnsubscribedUserBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        if (Request :: get(WeblcmsManager :: PARAM_TOOL_ACTION) != WeblcmsManager :: ACTION_SUBSCRIBE)
        {
            //$actions[WeblcmsManager :: PARAM_UNSUBSCRIBE_SELECTED] = Translation :: get('UnsubscribeSelected');
        }
        else
        {
            //$actions[WeblcmsManager :: PARAM_SUBSCRIBE_SELECTED_AS_STUDENT] = Translation :: get('SubscribeSelectedAsStudent');
        //$actions[WeblcmsManager :: PARAM_SUBSCRIBE_SELECTED_AS_ADMIN] = Translation :: get('SubscribeSelectedAsAdmin');
        }
        
        if ($browser->get_course()->is_course_admin($browser->get_user()))
        {
            //$this->set_form_actions($actions);
        }
        $this->set_default_row_count(20);
    }
}
?>