<?php
/**
 * @package cda.cda_manager.component.cda_language_browser
 */
require_once dirname(__FILE__).'/cda_language_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/cda_language_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/cda_language_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../cda_manager.class.php';

/**
 * Table to display a list of cda_languages
 *
 * @author Sven Vanpoucke
 * @author 
 */
class CdaLanguageBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'cda_language_browser_table';

	/**
	 * Constructor
	 */
	function CdaLanguageBrowserTable($browser, $parameters, $condition)
	{
		$model = new CdaLanguageBrowserTableColumnModel();
		$renderer = new CdaLanguageBrowserTableCellRenderer($browser);
		$data_provider = new CdaLanguageBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();

		$actions[] = new ObjectTableFormAction(CdaManager :: PARAM_DELETE_SELECTED_CDA_LANGUAGES, Translation :: get('RemoveSelected'));

		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>