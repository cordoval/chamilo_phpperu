<?php

namespace application\metadata;
use common\libraries\ObjectTable;
require_once dirname(__FILE__) . '/metadata_property_attribute_type_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/metadata_property_attribute_type_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/metadata_property_attribute_type_browser_table_column_model.class.php';

/**
 * Table to display a list of metadata_property_attribute_types
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataPropertyAttributeTypeBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'metadata_property_attribute_type_browser_table';

	/**
	 * Constructor
	 */
	function MetadataPropertyAttributeTypeBrowserTable($browser, $parameters, $condition)
	{
		$model = new MetadataPropertyAttributeTypeBrowserTableColumnModel();
		$renderer = new MetadataPropertyAttributeTypeBrowserTableCellRenderer($browser);
		$data_provider = new MetadataPropertyAttributeTypeBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();

		//$actions[] = new ObjectTableFormAction(MetadataManager :: PARAM_DELETE_SELECTED_METADATA_PROPERTY_ATTRIBUTE_TYPES, Translation :: get('RemoveSelected'));

		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>