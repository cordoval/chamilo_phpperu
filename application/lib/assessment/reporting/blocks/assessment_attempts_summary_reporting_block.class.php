<?php

require_once dirname(__FILE__) . '/../assessment_reporting_block.class.php';

class AssessmentAttemptsSummaryReportingBlock extends AssessmentReportingBlock
{
	public function count_data()
	{	
		$reporting_data = new ReportingData();
		
        return $reporting_data;
	}
	
	public function retrieve_data()
	{
        return $this->count_data();	
	}
/**
	 * 
	 */
	public function retrieve_date() {
		
	}


/**
	 * 
	 */
	function get_available_displaymodes() {
		
	}

}
?>