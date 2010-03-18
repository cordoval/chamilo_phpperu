<?php
require_once dirname(__FILE__).'/indicator_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/indicator_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/indicator_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../cba_manager.class.php';

/**
 * Table to display a list of indicators
 *
 * @author Nick Van Loocke
 */
class IndicatorBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'indicator_browser_table';

	/**
	 * Constructor
	 */
	function IndicatorBrowserTable($browser, $parameters, $condition)
	{
		$model = new IndicatorBrowserTableColumnModel();
		$renderer = new IndicatorBrowserTableCellRenderer($browser);
		$data_provider = new IndicatorBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		
		$actions[] = new ObjectTableFormAction(CbaManager :: PARAM_DELETE_SELECTED_INDICATORS, Translation :: get('RemoveSelected'));	
		$actions[] = new ObjectTableFormAction(CbaManager :: PARAM_MOVE_SELECTED_INDICATORS, Translation :: get('MoveSelected'), false);
		
		
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>