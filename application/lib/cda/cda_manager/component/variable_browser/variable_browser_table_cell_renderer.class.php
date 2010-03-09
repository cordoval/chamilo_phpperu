<?php
/**
 * @package cda.tables.variable_table
 */
require_once dirname(__FILE__).'/variable_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/variable_table/default_variable_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../variable.class.php';
require_once dirname(__FILE__).'/../../cda_manager.class.php';

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
		$toolbar_data = array();

		$can_edit = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, CdaRights :: LOCATION_VARIABLES, 'manager');
		$can_delete = CdaRights :: is_allowed(CdaRights :: DELETE_RIGHT, CdaRights :: LOCATION_VARIABLES, 'manager');

		if ($can_edit)
		{
    		$toolbar_data[] = array(
    			'href' => $this->browser->get_update_variable_url($variable),
    			'label' => Translation :: get('Edit'),
    			'img' => Theme :: get_common_image_path().'action_edit.png'
    		);
		}

		if ($can_delete)
		{
    		$toolbar_data[] = array(
    			'href' => $this->browser->get_delete_variable_url($variable),
    			'label' => Translation :: get('Delete'),
    			'img' => Theme :: get_common_image_path().'action_delete.png',
    		);
		}

		return Utilities :: build_toolbar($toolbar_data);
	}
}
?>