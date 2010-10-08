<?php
/**
 * Table to display a set of mediamosa external repository objects.
 */
require_once dirname(__file__) . '/mediamosa_external_repository_table_cell_renderer.class.php';
require_once dirname(__file__) . '/mediamosa_external_repository_table_data_provider.class.php';
require_once dirname(__file__) . '/mediamosa_external_repository_table_column_model.class.php';

class MediamosaExternalRepositoryTable extends ObjectTable
{
    const DEFAULT_NAME = 'mediamosa_external_repository_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function MediamosaExternalRepositoryTable($browser, $parameters, $condition)
    {
        $model = new MediamosaExternalRepositoryTableColumnModel();
        $renderer = new MediamosaExternalRepositoryTableCellRenderer($browser);
        $data_provider = new MediamosaExternalRepositoryTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);

        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>