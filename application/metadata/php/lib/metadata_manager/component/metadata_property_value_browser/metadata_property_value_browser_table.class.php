<?php


/**
 * @package metadata.metadata_manager.component.metadata_property_value_browser
 */
require_once dirname(__FILE__) . '/metadata_property_value_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/metadata_property_value_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/metadata_property_value_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../metadata_manager.class.php';

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