<?php

require_once dirname(__FILE__).'/gradebook_subscribe_user_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/gradebook_subscribe_user_table/default_gradebook_subscribe_user_table_cell_renderer.class.php';

class GradebookSubscribeUserBrowserTableCellRenderer extends DefaultGradebookSubscribeUserTableCellRenderer
{
	
	private $browser;
	
	function GradebookSubscribeUserBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $user)
	{
		if ($column === GradebookSubscribeUserBrowserTableColumnModel :: get_modification_column())
		{
			//return $this->get_modification_links( $user);
		}
		
		return parent :: render_cell($column, $user);
	}
	
	private function get_modification_links($user)
	{
		$toolbar_data = array();
		
//		$toolbar_data[] = array(
//			'href' => $this->browser->get_student_viewing_url($user->get_official_code()),
//			'label' => Translation :: get('ViewStudentDetails'),
//			'img' => Theme :: get_common_image_path().'action_subscribe.png'
//		);
		
			
		return Utilities :: build_toolbar($toolbar_data);
	}
}
?>