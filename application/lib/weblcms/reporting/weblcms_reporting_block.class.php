<?php
require_once PATH :: get_reporting_path() . '/lib/reporting_block.class.php';

abstract class WeblcmsReportingBlock extends ReportingBlock
{
	public function count_data()
	{}
	
	public function retrieve_data()
	{}
	
	public function get_data_manager()
	{
		return UserDataManager::get_instance();
	}
	
	public function get_available_diplaymodes()
	{
		
	}
	
	function get_application()
	{
		return WeblcmsManager::APPLICATION_NAME;
	}
}
?>