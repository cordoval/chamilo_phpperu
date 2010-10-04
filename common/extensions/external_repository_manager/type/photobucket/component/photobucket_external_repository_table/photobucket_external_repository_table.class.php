<?php
/**
 * Table to display a set of matterhorn external repository objects.
 */
require_once dirname(__file__) . '/photobucket_external_repository_table_cell_renderer.class.php';
require_once dirname(__file__) . '/photobucket_external_repository_table_data_provider.class.php';
require_once dirname(__file__) . '/photobucket_external_repository_table_column_model.class.php';

class PhotobucketExternalRepositoryTable extends ObjectTable
{
    const DEFAULT_NAME = 'photobucket_external_repository_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function PhotobucketExternalRepositoryTable($browser, $parameters, $condition)
    {
        $model = new PhotobucketExternalRepositoryTableColumnModel();
        $renderer = new PhotobucketExternalRepositoryTableCellRenderer($browser);
        $data_provider = new PhotobucketExternalRepositoryTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);

        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>