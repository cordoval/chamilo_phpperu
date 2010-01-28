<?php
/**
 * @package cda.cda_manager.component.translator_application_browser
 */
require_once dirname(__FILE__).'/translator_application_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/translator_application_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/translator_application_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../cda_manager.class.php';

/**
 * Table to display a list of translator_applications
 *
 * @author Sven Vanpoucke
 * @author 
 */
class TranslatorApplicationBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'translator_application_browser_table';

	/**
	 * Constructor
	 */
	function TranslatorApplicationBrowserTable($browser, $parameters, $condition)
	{
		$model = new TranslatorApplicationBrowserTableColumnModel();
		$renderer = new TranslatorApplicationBrowserTableCellRenderer($browser);
		$data_provider = new TranslatorApplicationBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();

//		$actions[] = new ObjectTableFormAction(CdaManager :: PARAM_DELETE_SELECTED_VARIABLES, Translation :: get('RemoveSelected'));

		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>