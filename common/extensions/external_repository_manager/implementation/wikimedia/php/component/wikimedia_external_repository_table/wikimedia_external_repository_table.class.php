<?php
namespace common\extensions\external_repository_manager\implementation\wikimedia;

use common\libraries\ObjectTable;
/**
 * Table to display a set of wikimedia external repository objects.
 */
require_once dirname(__file__) . '/wikimedia_external_repository_table_cell_renderer.class.php';
require_once dirname(__file__) . '/wikimedia_external_repository_table_data_provider.class.php';
require_once dirname(__file__) . '/wikimedia_external_repository_table_column_model.class.php';

class WikimediaExternalRepositoryTable extends ObjectTable
{
    const DEFAULT_NAME = 'wikimedia_external_repository_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new WikimediaExternalRepositoryTableColumnModel();
        $renderer = new WikimediaExternalRepositoryTableCellRenderer($browser);
        $data_provider = new WikimediaExternalRepositoryTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);

        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>