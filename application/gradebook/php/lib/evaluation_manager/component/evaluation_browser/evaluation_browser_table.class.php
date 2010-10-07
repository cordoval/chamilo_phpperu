 <?php
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'evaluation_manager/component/evaluation_browser/evaluation_browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'evaluation_manager/component/evaluation_browser/evaluation_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'evaluation_manager/component/evaluation_browser/evaluation_browser_table_cell_renderer.class.php';

class EvaluationBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'evaluation_browser_table';

	/**
	 * Constructor
	 */
	function EvaluationBrowserTable($browser)
	{
		$model = new EvaluationBrowserTableColumnModel();
		$renderer = new EvaluationBrowserTableCellRenderer($browser);
		$data_provider = new EvaluationBrowserTableDataProvider($browser);
		parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);

		$this->set_default_row_count(20);
	}
}
?>