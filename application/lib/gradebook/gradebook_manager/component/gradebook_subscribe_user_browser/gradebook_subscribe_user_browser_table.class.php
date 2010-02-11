<?php
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/gradebook_subscribe_user_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/gradebook_subscribe_user_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/gradebook_subscribe_user_browser_table_cell_renderer.class.php';

class GradebookSubscribeUserBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'gradebook_subscribe_user_browser_table';

	function GradebookSubscribeUserBrowserTable($browser, $parameters, $condition)
	{
	
		$model = new GradebookSubscribeUserBrowserTableColumnModel($browser);
		$renderer = new GradebookSubscribeUserBrowserTableCellRenderer($browser);
		$data_provider = new GradebookSubscribeUserBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, GradebookSubscribeUserBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$actions = array();
		$actions[] = new ObjectTableFormAction(GradebookManager :: PARAM_SUBSCRIBE_SELECTED,Translation :: get('Subscribe'));
		
		
		$this->set_additional_parameters($parameters);
		$user = $browser->get_user();
		$this->set_form_actions($actions);
		$this->set_default_row_count(10);
	}
}
?>