<?php

namespace application\assessment;

use common\libraries\WebApplication;
use common\libraries\Path;
use reporting\ReportingFormatter;
use common\libraries\Translation;

require_once dirname(__FILE__) . '/../assessment_reporting_block.class.php';

class AssessmentAttemptsReportingBlock extends AssessmentReportingBlock
{
	public function count_data()
	{	
        $file = WebApplication :: get_application_class_path($this->get_application()) . 'reporting/reporting_' . $this->get_application() . '.class.php';
        require_once $file;
        return ReportingAssessment :: getAssessmentAttempts($this->get_function_parameters());
        //return call_user_func('ReportingAssessment :: getAssessmentAttempts', $this->get_function_parameters());
	}
	
	public function retrieve_data()
	{
        return $this->count_data();
	}


/**
	 * 
	 */
	function get_available_displaymodes() 
	{
		$modes = array();
        $modes[ReportingFormatter::DISPLAY_TABLE] = Translation :: get('Table');
        return $modes;
	}

}
?>