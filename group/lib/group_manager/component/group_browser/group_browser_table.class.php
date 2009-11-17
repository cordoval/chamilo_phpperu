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
    const DEFAULT_NAME = 'group_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function GroupBrowserTable($browser, $parameters, $condition)
    {
        $model = new GroupBrowserTableColumnModel();
        $renderer = new GroupBrowserTableCellRenderer($browser);
        $data_provider = new GroupBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, GroupBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(GroupManager :: PARAM_REMOVE_SELECTED, Translation :: get('RemoveSelected'));
        $actions[] = new ObjectTableFormAction(GroupManager :: PARAM_TRUNCATE_SELECTED, Translation :: get('TruncateSelected'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>