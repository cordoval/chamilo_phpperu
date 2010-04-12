<?php
require_once dirname(__FILE__).'/../../../tables/evaluation_browser_table/default_evaluation_browser_table_cell_renderer.class.php';

class EvaluationBrowserTableCellRenderer extends DefaultEvaluationBrowserTableCellRenderer
{/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function EvaluationBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $evaluation)
	{
		if ($column === EvaluationBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($evaluation);
		}

		return parent :: render_cell($column, $evaluation);
	}

	/**
	 * Gets the action links to display
	 * @param Format $evaluation_format The evaluation format for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($evaluation)
	{
		$toolbar_data = array();
		if ($evaluation->get_evaluator_id() == $this->browser->get_user_id())
		{
        	$toolbar_data[] = array('href' => $this->browser->get_evaluation_editing_url($evaluation), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
        	$toolbar_data[] = array('href' => $this->browser->get_evaluation_deleting_url($evaluation), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png');
		}
        
		return Utilities :: build_toolbar($toolbar_data);
	}
}
