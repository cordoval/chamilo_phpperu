<?php
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'tables/evaluation_formats_table/default_evaluation_formats_table_cell_renderer.class.php';

class EvaluationFormatsBrowserTableCellRenderer extends DefaultEvaluationFormatsTableCellRenderer
{/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function EvaluationFormatsBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $format)
	{
		if ($column === EvaluationFormatsBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($format);
		}

		return parent :: render_cell($column, $format);
	}

	/**
	 * Gets the action links to display
	 * @param Format $evaluation_format The evaluation format for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($evaluation_format)
	{
		$toolbar = new Toolbar();
        $toolbar->add_item(new ToolbarItem(($evaluation_format->get_active() == 1) ? Translation :: get('Deactivate') : Translation :: get('Activate'), ($evaluation_format->get_active() == 1) ? Theme :: get_common_image_path() . 'action_visible.png' : Theme :: get_common_image_path() . 'action_invisible.png', $this->browser->get_change_evaluation_format_activation_url($evaluation_format), ToolbarItem :: DISPLAY_ICON));
        
		return $toolbar->as_html();
	}
	
}
?>