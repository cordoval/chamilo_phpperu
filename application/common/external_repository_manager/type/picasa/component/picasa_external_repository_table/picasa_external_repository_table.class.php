<?php
/**
 * Table to display a set of picasa external repository objects.
 */
require_once dirname(__file__) . '/picasa_external_repository_table_cell_renderer.class.php';
require_once dirname(__file__) . '/picasa_external_repository_table_data_provider.class.php';
require_once dirname(__file__) . '/picasa_external_repository_table_column_model.class.php';

class PicasaExternalRepositoryTable extends ObjectTable
{
    const DEFAULT_NAME = 'picasa_external_repository_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function PicasaExternalRepositoryTable($browser, $parameters, $condition)
    {
        $model = new PicasaExternalRepositoryTableColumnModel();
        $renderer = new PicasaExternalRepositoryTableCellRenderer($browser);
        $data_provider = new PicasaExternalRepositoryTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);

        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>