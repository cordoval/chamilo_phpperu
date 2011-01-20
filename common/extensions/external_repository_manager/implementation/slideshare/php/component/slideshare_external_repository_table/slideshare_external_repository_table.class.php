<?php
namespace common\extensions\external_repository_manager\implementation\slideshare;

use common\libraries\ObjectTable;
/**
 * Table to display a set of flickr external repository objects.
 */
require_once dirname(__file__) . '/slideshare_external_repository_table_cell_renderer.class.php';
require_once dirname(__file__) . '/slideshare_external_repository_table_data_provider.class.php';
require_once dirname(__file__) . '/slideshare_external_repository_table_column_model.class.php';

class SlideshareExternalRepositoryTable extends ObjectTable
{
    const DEFAULT_NAME = 'slideshare_external_repository_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new SlideshareExternalRepositoryTableColumnModel();
        $renderer = new SlideshareExternalRepositoryTableCellRenderer($browser);
        $data_provider = new SlideshareExternalRepositoryTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);

        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>