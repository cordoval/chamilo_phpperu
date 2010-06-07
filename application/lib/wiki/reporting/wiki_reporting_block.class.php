<?php
require_once PATH :: get_reporting_path() . '/lib/reporting_block.class.php';

abstract class WikiReportingBlock extends ReportingBlock
{
	public function get_data_manager()
	{
		return WikiDataManager::get_instance();
	}
	
	function get_application()
	{
		return Wiki :: APPLICATION_NAME;
	}
}
?>