<?php
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/gradebook_rel_user_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/gradebook_rel_user_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/gradebook_rel_user_browser_table_cell_renderer.class.php';

class GradebookRelUserBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'gradebook_rel_student_browser_table';
	
	
	function GradebookRelUserBrowserTable($browser, $parameters, $condition)
	{
		$model = new GradebookRelUserBrowserTableColumnModel();
		$renderer = new GradebookRelUserBrowserTableCellRenderer($browser);
		$data_provider = new GradebookRelUserBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, GradebookRelUserBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		$actions[] = new ObjectTableFormAction(GradebookManager :: PARAM_UNSUBSCRIBE_SELECTED, Translation :: get('UnsubscribeSelected'));
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
	
}
?>