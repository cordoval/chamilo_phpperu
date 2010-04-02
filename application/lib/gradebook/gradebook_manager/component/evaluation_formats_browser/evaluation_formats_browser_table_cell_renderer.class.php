<?php
require_once dirname(__FILE__).'/../../../tables/evaluation_formats_table/default_evaluation_formats_table_cell_renderer.class.php';

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

		switch ($column->get_name())
		{
			case Format :: PROPERTY_TITLE :
				return ucfirst($format->get_title());
				break;
			case Format :: PROPERTY_ACTIVE :
				return Utilities :: display_true_false_icon($format->get_active());
				break;
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
		$toolbar_data = array();
		
        $toolbar_data[] = array('href' => $this->browser->get_evaluation_format_editing_url($evaluation_format), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
        $toolbar_data[] = array('href' => $this->browser->get_evaluation_format_deleting_url($evaluation_format), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png');
        $toolbar_data[] = array('href' => $this->browser->get_change_evaluation_format_activation_url($evaluation_format), 'label' => ($evaluation_format->get_active() == 1) ? Translation :: get('Deactivate') : Translation :: get('Activate'), 'confirm' => false, 'img' => ($evaluation_format->get_active() == 1) ? Theme :: get_common_image_path() . 'action_visible.png' : Theme :: get_common_image_path() . 'action_invisible.png');
        
		return Utilities :: build_toolbar($toolbar_data);
	}
	
}
?>
