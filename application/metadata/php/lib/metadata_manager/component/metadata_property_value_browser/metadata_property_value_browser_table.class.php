<?php
namespace application\metadata;
use common\libraries\ObjectTable;

/**
 * Table to display a list of metadata_property_values
 *
 * @author Jens Vanderheyden
 */
class MetadataPropertyValueBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'metadata_property_value_browser_table';

    /**
     * Constructor
     */
    function MetadataPropertyValueBrowserTable($browser, $parameters, $condition)
    {
        $model = new MetadataPropertyValueBrowserTableColumnModel();
        $renderer = new MetadataPropertyValueBrowserTableCellRenderer($browser);
        $data_provider = new MetadataPropertyValueBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();

        //$actions[] = new ObjectTableFormAction(MetadataManager :: PARAM_DELETE_SELECTED_METADATA_PROPERTY_VALUES, Translation :: get('RemoveSelected'));

        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>