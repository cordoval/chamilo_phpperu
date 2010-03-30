<?php

require_once dirname (__FILE__) . '/../weblcms_reporting_block.class.php';

class WeblcmsNoOfCoursesReportingBlock extends WeblcmsReportingBlock
{
	public function count_data()
	{
		$wdm = WeblcmsDataManager :: get_instance();
        $count = $wdm->count_courses();

        $arr[Translation :: get('CourseCount')] = $count;

        return Reporting :: getSerieArray($arr);
	}	
	
	public function retrieve_data()
	{
		return $this->count_data();		
	}
	
	function get_application()
	{
		return UserManager::APPLICATION_NAME;
	}
	
	public function get_available_displaymodes()
	{
		$modes = array();
        $modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter::DISPLAY_TABLE] = Translation :: get('Table');
        $modes[ReportingChartFormatter::DISPLAY_PIE] = Translation :: get('Chart:Pie');
        return $modes;
	}
}
?>