<?php

require_once dirname(__FILE__).'/user_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/user_table/default_user_table_cell_renderer.class.php';

class SurveyUserBrowserTableCellRenderer extends DefaultSurveyUserTableCellRenderer
{
	
	private $browser;
	
	function SurveyUserBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $user)
	{
		if ($column === SurveyUserBrowserTableColumnModel :: get_modification_column())
		{
			//return $this->get_modification_links( $user);
		}
		
		return parent :: render_cell($column, $user);
	}
	
	private function get_modification_links($user)
	{
		$toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        
        return $toolbar->as_html();
	}
}
?>