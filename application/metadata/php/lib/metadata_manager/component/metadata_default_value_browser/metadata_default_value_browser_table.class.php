<?php

namespace application\metadata;
use common\libraries\ObjectTable;

require_once dirname(__FILE__) . '/metadata_default_value_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/metadata_default_value_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/metadata_default_value_browser_table_column_model.class.php';

/**
 * Table to display a list of metadata_default_values
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataDefaultValueBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'metadata_default_value_browser_table';

	/**
	 * Constructor
	 */
	function __construct($browser, $parameters, $condition)
	{
		$model = new MetadataDefaultValueBrowserTableColumnModel();
		$renderer = new MetadataDefaultValueBrowserTableCellRenderer($browser);
		$data_provider = new MetadataDefaultValueBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();

		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>