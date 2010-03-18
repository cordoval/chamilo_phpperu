<?php
require_once PATH :: get_reporting_path() . '/lib/reporting_block.class.php';

abstract class SurveyReportingBlock extends ReportingBlock
{
	public abstract function count_data();
	
	public abstract function retrieve_data();
	
	public function get_data_manager()
	{
		return SurveyDataManager::get_instance();
	}
}
?>