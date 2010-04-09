<?php
class ReportingViewerComponent extends SubManagerComponent
{
	function get_template()
	{
		return $this->get_parent()->get_template();
	}
	
	function set_template($template)
	{
		$this->get_parent()->set_template($template);
	}
	
	function get_breadcrumb_trail()
	{
		return $this->get_parent()->get_breadcrumb_trail();	
	}
	
	function set_breadcrumb_trail($trail)
	{
		$this->get_parent()->set_breadcrumb_trail($trail);	
	}
	
	function get_export()
	{
		$this->get_parent()->get_export();
	}
	
	function set_export($export)
	{
		$this->get_parent()->set_export($export);
	}
	
	function are_all_blocks_visible()
	{
		return $this->get_parent()->are_all_blocks_visible();
	}
}
?>