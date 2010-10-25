<?php

namespace application\assessment;

use reporting\ReportingBlock;
use common\libraries\Path;

require_once Path :: get_reporting_path() . '/lib/reporting_block.class.php';

abstract class AssessmentReportingBlock extends ReportingBlock
{
	function get_data_manager()
	{
		return AssessmentDataManager::get_instance();
	}
	
	function get_application()
	{
		return AssessmentManager::APPLICATION_NAME;
	}
}
?>