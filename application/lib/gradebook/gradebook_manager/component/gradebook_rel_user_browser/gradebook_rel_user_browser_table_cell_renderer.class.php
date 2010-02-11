<?php

require_once dirname(__FILE__).'/gradebook_rel_user_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/gradebook_rel_user_table/default_gradebook_rel_user_table_cell_renderer.class.php';

class GradebookRelUserBrowserTableCellRenderer extends DefaultGradebookRelUserTableCellRenderer
{
	
	private $browser;
	
	function GradebookRelUserBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $gradebookreluser)
	{
		
	
		if ($column === GradebookRelUserBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($gradebookreluser);
		}
		
		// Add special features here
		
		
		
		return parent :: render_cell($column, $gradebookreluser);
	}
	
	private function get_modification_links($gradebookreluser)
	{
		$toolbar_data = array();
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_gradebook_rel_user_unsubscribing_url($gradebookreluser),
			'label' => Translation :: get('Unsubscribe'),
			'img' => Theme :: get_common_image_path().'action_delete.png'
		);
		
		return Utilities :: build_toolbar($toolbar_data);
	}
}
?>