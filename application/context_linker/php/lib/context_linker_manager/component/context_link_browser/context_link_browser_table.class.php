<?php
namespace application\context_linker;
use common\libraries\ObjectTable;

require_once dirname(__FILE__) . '/context_link_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/context_link_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/context_link_browser_table_column_model.class.php';


/**
 * Table to display a list of context_links
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class ContextLinkBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'context_link_browser_table';

	/**
	 * Constructor
	 */
	function __construct($browser, $parameters, $condition)
	{
		$model = new ContextLinkBrowserTableColumnModel();
		$renderer = new ContextLinkBrowserTableCellRenderer($browser);
		$data_provider = new ContextLinkBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();

		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>