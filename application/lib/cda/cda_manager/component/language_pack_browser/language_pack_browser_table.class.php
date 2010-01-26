<?php
/**
 * @package cda.cda_manager.component.language_pack_browser
 */
require_once dirname(__FILE__).'/language_pack_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/language_pack_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/language_pack_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../cda_manager.class.php';

/**
 * Table to display a list of language_packs
 *
 * @author Sven Vanpoucke
 * @author 
 */
class LanguagePackBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'language_pack_browser_table';

	/**
	 * Constructor
	 */
	function LanguagePackBrowserTable($browser, $parameters, $condition)
	{
		$model = new LanguagePackBrowserTableColumnModel();
		$renderer = new LanguagePackBrowserTableCellRenderer($browser);
		$data_provider = new LanguagePackBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();

		$actions[] = new ObjectTableFormAction(CdaManager :: PARAM_DELETE_SELECTED_LANGUAGE_PACKS, Translation :: get('RemoveSelected'));

		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>