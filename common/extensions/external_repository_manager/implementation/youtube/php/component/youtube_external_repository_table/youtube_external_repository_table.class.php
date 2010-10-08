<?php
/**
 * Table to display a set of youtube external repository objects.
 */
require_once dirname(__file__) . '/youtube_external_repository_table_cell_renderer.class.php';
require_once dirname(__file__) . '/youtube_external_repository_table_data_provider.class.php';
require_once dirname(__file__) . '/youtube_external_repository_table_column_model.class.php';

class YoutubeExternalRepositoryTable extends ObjectTable
{
    const DEFAULT_NAME = 'youtube_external_repository_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function YoutubeExternalRepositoryTable($browser, $parameters, $condition)
    {
        $model = new YoutubeExternalRepositoryTableColumnModel();
        $renderer = new YoutubeExternalRepositoryTableCellRenderer($browser);
        $data_provider = new YoutubeExternalRepositoryTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);

        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>