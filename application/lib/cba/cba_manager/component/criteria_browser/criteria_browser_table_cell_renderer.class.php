<?php
require_once dirname(__FILE__).'/criteria_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/criteria_table/default_criteria_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../criteria.class.php';
require_once dirname(__FILE__).'/../../cba_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Nick Van Loocke
 */

class CriteriaBrowserTableCellRenderer extends DefaultCriteriaTableCellRenderer
{
	/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function CriteriaBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $criteria)
	{
		if ($column === CriteriaBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($criteria);
		}

		return parent :: render_cell($column, $criteria);
	}

	/**
	 * Gets the action links to display
	 * @param ContentObject $content_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($criteria)
	{
		$toolbar_data = array();

		$toolbar_data[] = array(
			'href' => $this->browser->get_update_criteria_url($criteria),
			'label' => Translation :: get('Edit'),
			'img' => Theme :: get_common_image_path().'action_edit.png'
		);

		$toolbar_data[] = array(
			'href' => $this->browser->get_delete_criteria_url($criteria),
			'label' => Translation :: get('Delete'),
			'img' => Theme :: get_common_image_path().'action_delete.png',
			'confirm' => true
		);

		return Utilities :: build_toolbar($toolbar_data);
	}
}
?>