<?php namespace application\cda;
/**
 * @package cda.cda_manager.component.language_pack_browser
 */
require_once WebApplication :: get_application_class_lib_path('cda') . 'cda_manager/component/language_pack_browser/language_pack_browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('cda') . 'cda_manager/component/language_pack_browser/language_pack_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('cda') . 'cda_manager/component/language_pack_browser/language_pack_browser_table_cell_renderer.class.php';

/**
 * Table to display a list of language_packs
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class LanguagePackBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'language_pack_browser_table';

	/**
	 * Constructor
	 */
	function LanguagePackBrowserTable($browser, $parameters, $condition)
	{
		$model = new LanguagePackBrowserTableColumnModel($browser);
		$renderer = new LanguagePackBrowserTableCellRenderer($browser);
		$data_provider = new LanguagePackBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();

		if(get_class($browser) != 'CdaManagerLanguagePacksBrowserComponent')
		{
			$actions[] = new ObjectTableFormAction(CdaManager :: PARAM_DELETE_SELECTED_LANGUAGE_PACKS, Translation :: get('RemoveSelected'));
		}

		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>