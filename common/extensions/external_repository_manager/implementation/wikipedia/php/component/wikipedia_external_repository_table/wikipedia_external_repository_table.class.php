<?php
namespace common\extensions\external_repository_manager\implementation\wikipedia;

use common\libraries\ObjectTable;
/**
 * Table to display a set of wikipedia external repository objects.
 */
require_once dirname(__file__) . '/wikipedia_external_repository_table_cell_renderer.class.php';
require_once dirname(__file__) . '/wikipedia_external_repository_table_data_provider.class.php';
require_once dirname(__file__) . '/wikipedia_external_repository_table_column_model.class.php';

class WikipediaExternalRepositoryTable extends ObjectTable
{
    const DEFAULT_NAME = 'wikipedia_external_repository_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new WikipediaExternalRepositoryTableColumnModel();
        $renderer = new WikipediaExternalRepositoryTableCellRenderer($browser);
        $data_provider = new WikipediaExternalRepositoryTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);

        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>