<?php

require_once dirname(__file__) . '/fedora_external_repository_table_cell_renderer.class.php';
require_once dirname(__file__) . '/fedora_external_repository_table_data_provider.class.php';
require_once dirname(__file__) . '/fedora_external_repository_table_column_model.class.php';

class FedoraExternalRepositoryTable extends ObjectTable
{
    const DEFAULT_NAME = 'fedora_external_repository_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new FedoraExternalRepositoryTableColumnModel();
        $renderer = new FedoraExternalRepositoryTableCellRenderer($browser);
        $data_provider = new FedoraExternalRepositoryTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);

        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }

}

?>