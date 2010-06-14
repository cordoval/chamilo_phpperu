<?php

require_once dirname(__FILE__).'/rel_group_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/period_rel_group_table/default_period_rel_group_table_cell_renderer.class.php';

class InternshipOrganizerPeriodGroupBrowserTableCellRenderer extends DefaultInternshipOrganizerPeriodGroupTableCellRenderer
{
	
	private $browser;
	
	function InternshipOrganizerPeriodGroupBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $rel_group)
	{
		if ($column === InternshipOrganizerPeriodGroupBrowserTableColumnModel :: get_modification_column())
		{
			//return $this->get_modification_links( $rel_group);
		}
		
		return parent :: render_cell($column, $rel_group);
	}
	
	private function get_modification_links($rel_group)
	{
		$toolbar_data = array();
		
		
		
		return Utilities :: build_toolbar($toolbar_data);
	}
}
?>