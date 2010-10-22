<?php namespace application\cda;
/**
 * @package cda.tables.variable_table
 */
require_once WebApplication :: get_application_class_lib_path('cda') . 'cda_manager/component/variable_browser/variable_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('cda') . 'tables/variable_table/default_variable_table_cell_renderer.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class VariableBrowserTableCellRenderer extends DefaultVariableTableCellRenderer
{
	/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function VariableBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $variable)
	{
		if ($column === VariableBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($variable);
		}

		return parent :: render_cell($column, $variable);
	}

	/**
	 * Gets the action links to display
	 * @param ContentObject $content_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($variable)
	{
		$toolbar = new Toolbar();

		$can_edit = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, CdaRights :: LOCATION_VARIABLES, 'manager');
		$can_delete = CdaRights :: is_allowed(CdaRights :: DELETE_RIGHT, CdaRights :: LOCATION_VARIABLES, 'manager');

		if ($can_edit)
		{
			$toolbar->add_item(new ToolbarItem(
    				Translation :: get('Edit'), 
    				Theme :: get_common_image_path() . 'action_edit.png', 
    				$this->browser->get_update_variable_url($variable), 
    				ToolbarItem :: DISPLAY_ICON
    				));
		}

		if ($can_delete)
		{
			$toolbar->add_item(new ToolbarItem(
    				Translation :: get('Delete'), 
    				Theme :: get_common_image_path() . 'action_delete.png', 
    				$this->browser->get_delete_variable_url($variable), 
    				ToolbarItem :: DISPLAY_ICON,
    				true
    				));
		}

		return $toolbar->as_html();
	}
}
?>