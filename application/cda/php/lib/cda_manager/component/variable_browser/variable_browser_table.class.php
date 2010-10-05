<?php
/**
 * @package cda.cda_manager.component.variable_browser
 */
require_once dirname(__FILE__).'/variable_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/variable_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/variable_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../cda_manager.class.php';

/**
 * Table to display a list of variables
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class VariableBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'variable_browser_table';

	/**
	 * Constructor
	 */
	function VariableBrowserTable($browser, $parameters, $condition)
	{
		$model = new VariableBrowserTableColumnModel();
		$renderer = new VariableBrowserTableCellRenderer($browser);
		$data_provider = new VariableBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();

		$actions[] = new ObjectTableFormAction(CdaManager :: PARAM_DELETE_SELECTED_VARIABLES, Translation :: get('RemoveSelected'));

		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>