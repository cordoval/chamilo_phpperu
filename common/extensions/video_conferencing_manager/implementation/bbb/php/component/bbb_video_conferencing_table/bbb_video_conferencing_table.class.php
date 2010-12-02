<?php
namespace common\extensions\video_conferencing_manager\implementation\bbb;

use common\libraries\ObjectTable;
/**
 * Table to display a set of flickr external repository objects.
 */
require_once dirname(__file__) . '/bbb_video_conferencing_table_cell_renderer.class.php';
require_once dirname(__file__) . '/bbb_video_conferencing_table_data_provider.class.php';
require_once dirname(__file__) . '/bbb_video_conferencing_table_column_model.class.php';

class BbbVideoConferencingTable extends ObjectTable
{
    const DEFAULT_NAME = 'bbb_video_conferencing_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new BbbVideoConferencingTableColumnModel();
        $renderer = new BbbVideoConferencingTableCellRenderer($browser);
        $data_provider = new BbbVideoConferencingTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);

        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>