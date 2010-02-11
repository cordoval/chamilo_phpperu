<?php

require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/gradebook_score_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/gradebook_score_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/gradebook_score_browser_table_cell_renderer.class.php';

class GradebookScoreBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'gradebook_score_browser_table';
	
	function GradebookScoreBrowserTable($browser, $parameters, $condition)
	{
		$model = new GradebookScoreBrowserTableColumnModel();
		$renderer = new GradebookScoreBrowserTableCellRenderer($browser);
		$data_provider = new GradebookScoreBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, GradebookScoreBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		//$actions[] = new ObjectTableFormAction(GradebookManager :: PARAM_UNSUBSCRIBE_SELECTED, Translation :: get('UnsubscribeSelected'));
		$this->set_form_actions($actions);
		$this->set_default_row_count(10);
	}
	
}
?>