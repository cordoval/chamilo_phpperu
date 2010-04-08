<?php
require_once PATH :: get_reporting_path() . '/lib/reporting_block.class.php';

abstract class WeblcmsReportingBlock extends ReportingBlock
{
	public function get_data_manager()
	{
		return UserDataManager::get_instance();
	}
	
	function get_application()
	{
		return WeblcmsManager::APPLICATION_NAME;
	}
}
?>