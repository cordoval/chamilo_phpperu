<?php
/**
 * $Id: group_browser_table.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package groups.lib.group_manager.component.group_browser
 */
require_once dirname(__FILE__) . '/group_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/group_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/group_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class GroupBrowserTable extends ObjectTable
{
    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function GroupBrowserTable($browser, $parameters, $condition)
    {
        $model = new GroupBrowserTableColumnModel();
        $renderer = new GroupBrowserTableCellRenderer($browser);
        $data_provider = new GroupBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, Utilities :: camelcase_to_underscores(__CLASS__), $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(GroupManager :: ACTION_DELETE_GROUP, Translation :: get('RemoveSelected'));
        $actions[] = new ObjectTableFormAction(GroupManager :: ACTION_TRUNCATE_GROUP, Translation :: get('TruncateSelected'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
    
    static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(GroupManager :: PARAM_GROUP_ID, $ids);
    }
}
?>