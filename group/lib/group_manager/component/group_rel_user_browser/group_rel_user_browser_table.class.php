<?php
/**
 * $Id: group_rel_user_browser_table.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package groups.lib.group_manager.component.group_rel_user_browser
 */
require_once dirname(__FILE__) . '/group_rel_user_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/group_rel_user_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/group_rel_user_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class GroupRelUserBrowserTable extends ObjectTable
{
    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function GroupRelUserBrowserTable($browser, $parameters, $condition)
    {
        $model = new GroupRelUserBrowserTableColumnModel();
        $renderer = new GroupRelUserBrowserTableCellRenderer($browser);
        $data_provider = new GroupRelUserBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, Utilities :: camelcase_to_underscores(__CLASS__), $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(GroupManager :: ACTION_UNSUBSCRIBE_USER_FROM_GROUP, Translation :: get('UnsubscribeSelected'), false);
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }

	static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(GroupManager :: PARAM_GROUP_REL_USER_ID, $ids);
    }
    
    /**
     * A typical ObjectTable would get the database-id of the object as a
     * unique identifier. GroupRelUser has no such field since it's
     * a relation, so we need to overwrite this function here.
     */
    function get_objects($offset, $count, $order_column)
    {
        $grouprelusers = $this->get_data_provider()->get_objects($offset, $count, $this->get_column_model()->get_order_column($order_column - ($this->has_form_actions() ? 1 : 0)));
        $table_data = array();
        $column_count = $this->get_column_model()->get_column_count();
        while ($groupreluser = $grouprelusers->next_result())
        {
            $row = array();
            if ($this->has_form_actions())
            {
                $row[] = $groupreluser->get_group_id() . '|' . $groupreluser->get_user_id();
            }
            for($i = 0; $i < $column_count; $i ++)
            {
                $row[] = $this->get_cell_renderer()->render_cell($this->get_column_model()->get_column($i), $groupreluser);
            }
            $table_data[] = $row;
        }
        return $table_data;
    }
}
?>