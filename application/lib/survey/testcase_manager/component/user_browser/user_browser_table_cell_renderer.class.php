<?php

require_once dirname(__FILE__).'/user_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/user_table/default_user_table_cell_renderer.class.php';

class TestCaseManagerUserBrowserTableCellRenderer extends DefaultTestCaseManagerUserTableCellRenderer
{
	
	private $browser;
	
	function TestCaseManagerUserBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $user)
	{
		if ($column === TestCaseManagerUserBrowserTableColumnModel :: get_modification_column())
		{
			//return $this->get_modification_links( $user);
		}
		
		return parent :: render_cell($column, $user);
	}
	
	private function get_modification_links($user)
	{
		$toolbar_data = array();
		
		
		
		return Utilities :: build_toolbar($toolbar_data);
	}
}
?>