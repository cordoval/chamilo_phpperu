<?php

require_once PATH::get_reporting_path() . '/lib/reporting_block.class.php';

abstract class AssessmentReportingBlock extends ReportingBlock
{
	function get_data_manager()
	{
		return AssessmentDataManager::get_instance();
	}
	
	public abstract function count_data();
	public abstract function retrieve_date();
}
?>