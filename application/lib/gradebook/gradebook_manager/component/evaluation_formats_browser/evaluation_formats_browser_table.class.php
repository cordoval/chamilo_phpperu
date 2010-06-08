<?php
require_once dirname(__FILE__).'/evaluation_formats_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/evaluation_formats_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/evaluation_formats_browser_table_cell_renderer.class.php';

class EvaluationFormatsBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'evaluation_formats_browser_table';

	/**
	 * Constructor
	 */
	function EvaluationFormatsBrowserTable($browser, $parameters)
	{
		$model = new EvaluationFormatsBrowserTableColumnModel($browser);
		$renderer = new EvaluationFormatsBrowserTableCellRenderer($browser);
		$data_provider = new EvaluationFormatsBrowserTableDataProvider($browser);
		parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		
        $actions[] = new ObjectTableFormAction(GradebookManager :: PARAM_ACTIVATE_SELECTED_EVALUATION_FORMAT, Translation :: get('ActivateSelected'), false);
        $actions[] = new ObjectTableFormAction(GradebookManager :: PARAM_DEACTIVATE_SELECTED_EVALUATION_FORMAT, Translation :: get('DeactivateSelected'), false);
		
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>