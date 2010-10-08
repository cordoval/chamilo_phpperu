<?php
/**
 * Table to display a set of google_docs external repository objects.
 */
require_once dirname(__file__) . '/google_docs_external_repository_table_cell_renderer.class.php';
require_once dirname(__file__) . '/google_docs_external_repository_table_data_provider.class.php';
require_once dirname(__file__) . '/google_docs_external_repository_table_column_model.class.php';

class GoogleDocsExternalRepositoryTable extends ObjectTable
{
    const DEFAULT_NAME = 'google_docs_external_repository_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function GoogleDocsExternalRepositoryTable($browser, $parameters, $condition)
    {
        $model = new GoogleDocsExternalRepositoryTableColumnModel();
        $renderer = new GoogleDocsExternalRepositoryTableCellRenderer($browser);
        $data_provider = new GoogleDocsExternalRepositoryTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);

        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>