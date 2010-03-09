<?php
require_once dirname(__FILE__).'/indicator_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/indicator_table/default_indicator_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../indicator.class.php';
require_once dirname(__FILE__).'/../../cba_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author Nick Van Loocke
 */

class IndicatorBrowserTableCellRenderer extends DefaultIndicatorTableCellRenderer
{
	/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function IndicatorBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $indicator)
	{
		if ($column === IndicatorBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($indicator);
		}

		return parent :: render_cell($column, $indicator);
	}

	/**
	 * Gets the action links to display
	 * @param ContentObject $content_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($indicator)
	{
		$toolbar_data = array();

		$toolbar_data[] = array(
			'href' => $this->browser->get_update_indicator_url($indicator),
			'label' => Translation :: get('Edit'),
			'img' => Theme :: get_common_image_path().'action_edit.png'
		);

		$toolbar_data[] = array(
			'href' => $this->browser->get_delete_indicator_url($indicator),
			'label' => Translation :: get('Delete'),
			'img' => Theme :: get_common_image_path().'action_delete.png',
		);

		return Utilities :: build_toolbar($toolbar_data);
	}
}
?>