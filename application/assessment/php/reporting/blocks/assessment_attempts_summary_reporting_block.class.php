<?php

namespace application\assessment;

use common\libraries\WebApplication;
use common\libraries\Path;
use reporting\ReportingFormatter;
use common\libraries\Translation;

require_once dirname(__FILE__) . '/../assessment_reporting_block.class.php';

class AssessmentAttemptsSummaryReportingBlock extends AssessmentReportingBlock
{
	public function count_data()
	{	
		$base_path = (WebApplication :: is_application($this->get_application()) ? Path :: get_application_path() : Path :: get(SYS_PATH));
        
        $file = $base_path . $this->get_application() . '/php/reporting/reporting_' . $this->get_application() . '.class.php';
        require_once $file;
        return ReportingAssessment :: getSummaryAssessmentAttempts($this->get_function_parameters());
	}
	
	public function retrieve_data()
	{
        return $this->count_data();	
	}

/**
	 * 
	 */
	function get_available_displaymodes() {
		$modes = array();
        $modes[ReportingFormatter::DISPLAY_TABLE] = Translation :: get('Table');
        return $modes;
	}

}
?>