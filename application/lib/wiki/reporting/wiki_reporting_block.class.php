<?php
require_once PATH :: get_reporting_path() . '/lib/reporting_block.class.php';

abstract class WikiReportingBlock extends ReportingBlock
{
	public function get_data_manager()
	{
		return WikiDataManager::get_instance();
	}
	
	function get_publication_id()
	{
		return $this->get_parent()->get_parameter(WikiManager :: PARAM_WIKI_PUBLICATION);
	}
	
	function get_application()
	{
		return WikiManager :: APPLICATION_NAME;
	}
}
?>