<?php
require_once PATH :: get_reporting_path() . '/lib/reporting_block.class.php';

abstract class AdminReportingBlock extends ReportingBlock
{	
	public function get_data_manager()
	{
		return AdminDataManager::get_instance();
	}
}
?>