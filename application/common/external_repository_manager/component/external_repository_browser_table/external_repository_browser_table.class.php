<?php
/**
 * $Id: repository_browser_table.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
require_once dirname(__FILE__) . '/external_repository_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/external_repository_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/external_repository_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class ExternalRepositoryBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'external_repository_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function ExternalRepositoryBrowserTable($browser, $parameters, $condition)
    {
        $model = new ExternalRepositoryBrowserTableColumnModel();
        $renderer = new ExternalRepositoryBrowserTableCellRenderer($browser);
        $data_provider = new ExternalRepositoryBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, ExternalRepositoryBrowserTable :: DEFAULT_NAME, $model, $renderer);

        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }

//    static function handle_table_action()
//    {
//        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
//        Request :: set_get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID, $ids);
//    }
}
?>