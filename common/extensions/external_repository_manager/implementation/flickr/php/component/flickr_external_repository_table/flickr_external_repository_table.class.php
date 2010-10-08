<?php
/**
 * Table to display a set of flickr external repository objects.
 */
require_once dirname(__file__) . '/flickr_external_repository_table_cell_renderer.class.php';
require_once dirname(__file__) . '/flickr_external_repository_table_data_provider.class.php';
require_once dirname(__file__) . '/flickr_external_repository_table_column_model.class.php';

class FlickrExternalRepositoryTable extends ObjectTable
{
    const DEFAULT_NAME = 'flickr_external_repository_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function FlickrExternalRepositoryTable($browser, $parameters, $condition)
    {
        $model = new FlickrExternalRepositoryTableColumnModel();
        $renderer = new FlickrExternalRepositoryTableCellRenderer($browser);
        $data_provider = new FlickrExternalRepositoryTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);

        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>