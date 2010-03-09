<?php
require_once dirname(__FILE__).'/competency_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/competency_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/competency_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../cba_manager.class.php';

/**
 * Table to display a list of competencys
 *
 * @author Sven Vanpoucke
 * @author Nick Van Loocke
 */
class CompetencyBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'competency_browser_table';

	/**
	 * Constructor
	 */
	function CompetencyBrowserTable($browser, $parameters, $condition)
	{
		$model = new CompetencyBrowserTableColumnModel();
		$renderer = new CompetencyBrowserTableCellRenderer($browser);
		$data_provider = new CompetencyBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();

		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>