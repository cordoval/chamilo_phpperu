<?php

namespace application\package;

use common\libraries\WebApplication;
use common\libraries\ObjectTable;
/**
 * @package package.package_manager.component.historic_variable_translation_browser
 */
require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/historic_variable_translation_browser/historic_variable_translation_browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/historic_variable_translation_browser/historic_variable_translation_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/historic_variable_translation_browser/historic_variable_translation_browser_table_cell_renderer.class.php';

/**
 * Table to display a list of historic_variable_translations
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class HistoricVariableTranslationBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'historic_variable_translation_browser_table';

	/**
	 * Constructor
	 */
	function __construct($browser, $parameters, $condition)
	{
		$model = new HistoricVariableTranslationBrowserTableColumnModel();
		$renderer = new HistoricVariableTranslationBrowserTableCellRenderer($browser);
		$data_provider = new HistoricVariableTranslationBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
//		$actions[] = new ObjectTableFormAction(PackageManager :: PARAM_COMPARE_SELECTED_VARIABLE_TRANSLATIONS, Translation :: get('CompareSelected'));

		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>