<?php
/**
 * @package cda.cda_manager.component.variable_translation_browser
 */
require_once dirname(__FILE__).'/variable_translation_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/variable_translation_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/variable_translation_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../cda_manager.class.php';

/**
 * Table to display a list of variable_translations
 *
 * @author Sven Vanpoucke
 * @author 
 */
class VariableTranslationBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'variable_translation_browser_table';

	/**
	 * Constructor
	 */
	function VariableTranslationBrowserTable($browser, $parameters, $condition)
	{
		$model = new VariableTranslationBrowserTableColumnModel();
		$renderer = new VariableTranslationBrowserTableCellRenderer($browser);
		$data_provider = new VariableTranslationBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();


		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>