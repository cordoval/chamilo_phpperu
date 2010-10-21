<?php
namespace application\metadata;
use common\libraries\ObjectTable;

/**
 * Table to display a set of users.
 */
class MetadataNamespaceBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'metadata_namespace_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function MetadataNamespaceBrowserTable($browser, $parameters, $condition)
    {
        $model = new MetadataNamespaceBrowserTableColumnModel();
        $renderer = new MetadataNamespaceBrowserTableCellRenderer($browser);
        $data_provider = new MetadataNamespaceBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, MetadataNamespaceBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>