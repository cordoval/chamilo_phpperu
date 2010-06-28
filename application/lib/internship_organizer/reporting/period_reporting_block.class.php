<?php
require_once dirname ( __FILE__ ) . '/internship_organizer_reporting_block.class.php';

abstract class InternshipOrganizerPeriodReportingBlock extends InternshipOrganizerReportingBlock
{
		
	function get_period_id()
	{
		return $this->get_parent()->get_parameter(InternshipOrganizerPeriodManager::PARAM_PERIOD_ID);	
	}

}
?>