<?php
namespace application\context_linker;
use common\libraries\ObjectTable;
require_once dirname(__FILE__) . '/content_object_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/content_object_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/content_object_browser_table_column_model.class.php';
/**
 * Table to display a list of content_objects
 *
 * @author Jens Vanderheyden
 */
class ContentObjectBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'content_object_browser_table';

	/**
	 * Constructor
	 */
	function __construct($browser, $parameters, $condition)
	{
		$model = new ContentObjectBrowserTableColumnModel();
		$renderer = new ContentObjectBrowserTableCellRenderer($browser);
		$data_provider = new ContentObjectBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();

		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>