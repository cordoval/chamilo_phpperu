<?php
/**
 * $Id: repository_version_browser_table.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
require_once dirname(__FILE__) . '/repository_version_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/repository_version_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/repository_version_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class RepositoryVersionBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'repository_version_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function RepositoryVersionBrowserTable($browser, $parameters, $condition)
    {
        $model = new RepositoryVersionBrowserTableColumnModel();
        $renderer = new RepositoryVersionBrowserTableCellRenderer($browser);
        $data_provider = new RepositoryVersionBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, RepositoryVersionBrowserTable :: DEFAULT_NAME, $model, $renderer);
        
        $actions = new ObjectTableFormActions();
        $actions->add_form_action(new ObjectTableFormAction(RepositoryManager :: ACTION_COMPARE_CONTENT_OBJECTS, Translation :: get('CompareSelected')));
        
        $this->set_additional_parameters($parameters);
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