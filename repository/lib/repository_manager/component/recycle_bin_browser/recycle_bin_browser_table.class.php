<?php
/**
 * $Id: recycle_bin_browser_table.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.recycle_bin_browser
 */
require_once dirname(__FILE__) . '/recycle_bin_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/recycle_bin_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/recycle_bin_browser_table_cell_renderer.class.php';
/**
 * This class provides the table to display all learning objects in the recycle
 * bin.
 */
class RecycleBinBrowserTable extends ObjectTable
{
    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function RecycleBinBrowserTable($browser, $parameters, $condition)
    {
        $model = new RecycleBinBrowserTableColumnModel();
        $renderer = new RecycleBinBrowserTableCellRenderer($browser);
        $data_provider = new RecycleBinBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, Utilities :: camelcase_to_underscores(__CLASS__), $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(RepositoryManager :: ACTION_RESTORE_CONTENT_OBJECTS, Translation :: get('RestoreSelected'));
        $actions[] = new ObjectTableFormAction(RepositoryManager :: ACTION_DELETE_CONTENT_OBJECTS_PERMANENTLY, Translation :: get('DeleteSelected'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
    
	static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID, $ids);
    }
}
?>