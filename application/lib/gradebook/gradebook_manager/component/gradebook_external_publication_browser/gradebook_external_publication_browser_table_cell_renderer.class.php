<?php
require_once dirname(__FILE__).'/../../../tables/gradebook_publication_table/default_gradebook_publication_table_cell_renderer.class.php';
require_once Path :: get_common_path() . '/datetime/datetime_utilities.class.php';

class GradebookExternalPublicationBrowserTableCellRenderer extends DefaultGradebookPublicationTableCellRenderer
{/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function GradebookExternalPublicationBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $external_item)
	{
		if ($column === GradebookExternalPublicationBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($external_item);
		}

		return parent :: render_cell($column, $external_item);
	}

	/**
	 * Gets the action links to display
	 * @param Format $evaluation_format The evaluation format for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($external_item)
	{
		$toolbar_data = array();
        $toolbar_data[] = array('href' => $this->browser->get_external_evaluations_on_publications_viewer_url($external_item), 'img' => Theme :: get_common_image_path() . 'action_browser.png');
        
        $user = $this->browser->get_user();
        
        if ($user->is_platform_admin())
        {
	        $toolbar_data[] = array('href' => $this->browser->get_edit_external_evaluation_url($external_item), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
	        $toolbar_data[] = array('href' => $this->browser->get_delete_external_evaluation_url($external_item), 'img' => Theme :: get_common_image_path() . 'action_delete.png');
        }
        
		return Utilities :: build_toolbar($toolbar_data);
	}
	
}
?>