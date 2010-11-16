<?php
namespace common\extensions\external_repository_manager\implementation\dropbox;
/**
 * Table to display a set of dropbox external repository objects.
 */
require_once dirname(__file__) . '/dropbox_external_repository_table_cell_renderer.class.php';
require_once dirname(__file__) . '/dropbox_external_repository_table_data_provider.class.php';
require_once dirname(__file__) . '/dropbox_external_repository_table_column_model.class.php';

class DropboxExternalRepositoryTable extends ObjectTable
{
    const DEFAULT_NAME = 'dropbox_external_repository_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function DropboxExternalRepositoryTable($browser, $parameters, $condition)
    {
        $model = new DropboxExternalRepositoryTableColumnModel();
        $renderer = new DropboxExternalRepositoryTableCellRenderer($browser);
        $data_provider = new DropboxExternalRepositoryTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);

        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>