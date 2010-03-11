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
		$toolbar_data = array();

		$toolbar_data[] = array(
			'href' => $this->browser->get_update_competency_url($competency),
			'label' => Translation :: get('Edit'),
			'img' => Theme :: get_common_image_path().'action_edit.png'
		);
		//if ($this->browser->count_competency_categories() > 0)
        //{
			$toolbar_data[] = array(
				'href' => $this->browser->get_competency_moving_url($competency),
				'label' => Translation :: get('Move'),
				'img' => Theme :: get_common_image_path().'action_move.png'
			);
        //}   		

		$toolbar_data[] = array(
			'href' => $this->browser->get_delete_competency_url($competency),
			'label' => Translation :: get('Delete'),
			'img' => Theme :: get_common_image_path().'action_delete.png',
			'confirm' => true
		);

		return Utilities :: build_toolbar($toolbar_data);
	}
}
?>