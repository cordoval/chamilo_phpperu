<?php

namespace application\package;

use common\libraries\WebApplication;
use common\libraries\ObjectTable;
/**
 * @package package.package_manager.component.variable_translation_browser
 */
require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/variable_translation_browser/variable_translation_browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/variable_translation_browser/variable_translation_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/variable_translation_browser/variable_translation_browser_table_cell_renderer.class.php';

/**
 * Table to display a list of variable_translations
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class VariableTranslationBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'variable_translation_browser_table';

	/**
	 * Constructor
	 */
	function __construct($browser, $parameters, $condition)
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