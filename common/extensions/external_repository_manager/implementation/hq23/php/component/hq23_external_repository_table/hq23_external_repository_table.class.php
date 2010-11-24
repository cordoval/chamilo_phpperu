<?php
namespace common\extensions\external_repository_manager\implementation\hq23;
use common\libraries\ObjectTable;
/**
 * Table to display a set of hq23 external repository objects.
 */
require_once dirname(__file__) . '/hq23_external_repository_table_cell_renderer.class.php';
require_once dirname(__file__) . '/hq23_external_repository_table_data_provider.class.php';
require_once dirname(__file__) . '/hq23_external_repository_table_column_model.class.php';

class Hq23ExternalRepositoryTable extends ObjectTable
{
    const DEFAULT_NAME = 'hq23_external_repository_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new Hq23ExternalRepositoryTableColumnModel();
        $renderer = new Hq23ExternalRepositoryTableCellRenderer($browser);
        $data_provider = new Hq23ExternalRepositoryTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);

        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>