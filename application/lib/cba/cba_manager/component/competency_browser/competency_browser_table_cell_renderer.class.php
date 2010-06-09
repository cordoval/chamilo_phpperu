<?php
require_once dirname(__FILE__).'/competency_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/competency_table/default_competency_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../competency.class.php';
require_once dirname(__FILE__).'/../../cba_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Nick Van Loocke
 */

class CompetencyBrowserTableCellRenderer extends DefaultCompetencyTableCellRenderer
{
	/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function CompetencyBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $competency)
	{
		if ($column === CompetencyBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($competency);
		}

		return parent :: render_cell($column, $competency);
	}

	/**
	 * Gets the action links to display
	 * @param ContentObject $content_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($competency)
	{
		$toolbar = new Toolbar();
		
		$toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_update_competency_url($competency), ToolbarItem :: DISPLAY_ICON));

		$toolbar->add_item(new ToolbarItem(Translation :: get('Move'), Theme :: get_common_image_path() . 'action_move.png', $this->browser->get_competency_moving_url($competency), ToolbarItem :: DISPLAY_ICON));

		$toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png',$this->browser->get_delete_competency_url($competency), ToolbarItem :: DISPLAY_ICON, true));

		return $toolbar->as_html();
	}
}
?>