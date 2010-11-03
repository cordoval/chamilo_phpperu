<?php

namespace application\metadata;
use common\libraries\ObjectTable;
require_once dirname(__FILE__) . '/user_metadata_property_value_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/user_metadata_property_value_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/user_metadata_property_value_browser_table_column_model.class.php';
/**
 * Table to display a list of metadata_property_values
 *
 * @author Jens Vanderheyden
 */
class UserMetadataPropertyValueBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'user_metadata_property_value_browser_table';

    /**
     * Constructor
     */
    function UserMetadataPropertyValueBrowserTable($browser, $parameters, $condition)
    {
        $model = new UserMetadataPropertyValueBrowserTableColumnModel();
        $renderer = new UserMetadataPropertyValueBrowserTableCellRenderer($browser);
        $data_provider = new UserMetadataPropertyValueBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();

        //$actions[] = new ObjectTableFormAction(MetadataManager :: PARAM_DELETE_SELECTED_METADATA_PROPERTY_VALUES, Translation :: get('RemoveSelected'));

        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>