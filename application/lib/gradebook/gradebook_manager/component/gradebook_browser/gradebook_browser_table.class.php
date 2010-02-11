<?php
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/gradebook_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/gradebook_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/gradebook_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../gradebook_manager.class.php';

class GradebookBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'gradebook_browser_table';
	
	function GradebookBrowserTable($browser, $parameters, $condition)
	{
		$model = new GradebookBrowserTableColumnModel();
		$renderer = new GradebookBrowserTableCellRenderer($browser);
		$data_provider = new GradebookBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, GradebookBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		$actions[] = new ObjectTableFormAction(GradebookManager :: PARAM_REMOVE_SELECTED, Translation :: get('RemoveSelected'));
		$actions[] = new ObjectTableFormAction(GradebookManager :: PARAM_TRUNCATE_SELECTED, Translation :: get('TruncateSelected'));
			
		$this->set_form_actions($actions);
		$this->set_default_row_count(10);
	}
}
?>