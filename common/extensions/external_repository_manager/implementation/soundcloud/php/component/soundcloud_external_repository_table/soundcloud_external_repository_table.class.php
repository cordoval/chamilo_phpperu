<?php
namespace common\extensions\external_repository_manager\implementation\soundcloud;

use common\libraries\ObjectTable;
/**
 * Table to display a set of soundcloud external repository objects.
 */
require_once dirname(__file__) . '/soundcloud_external_repository_table_cell_renderer.class.php';
require_once dirname(__file__) . '/soundcloud_external_repository_table_data_provider.class.php';
require_once dirname(__file__) . '/soundcloud_external_repository_table_column_model.class.php';

class SoundcloudExternalRepositoryTable extends ObjectTable
{
    const DEFAULT_NAME = 'soundcloud_external_repository_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new SoundcloudExternalRepositoryTableColumnModel();
        $renderer = new SoundcloudExternalRepositoryTableCellRenderer($browser);
        $data_provider = new SoundcloudExternalRepositoryTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);

        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>