<?php

require_once dirname(__FILE__).'/user_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/user_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/user_browser_table_cell_renderer.class.php';

class SurveyUserBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'user_browser_table';

	/**
	 * Constructor
	 */
	function SurveyUserBrowserTable($browser, $parameters, $condition)
	{
		
	
		$model = new SurveyUserBrowserTableColumnModel($browser);
		$renderer = new SurveyUserBrowserTableCellRenderer($browser);
		$data_provider = new SurveyUserBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider,SurveyUserBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$actions = array();
		$this->set_additional_parameters($parameters);
		$this->set_form_actions($actions);
		
	}
}
?>