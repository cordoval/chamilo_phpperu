<?php

require_once dirname (__FILE__) . '/../weblcms_reporting_block.class.php';

class WeblcmsNoOfCoursesByLanguageReportingBlock extends WeblcmsReportingBlock
{
	public function count_data()
	{
		$wdm = WeblcmsDataManager :: get_instance();
        $arr = array();
        $courses = $wdm->retrieve_courses();
        while ($course = $courses->next_result())
        {
            $lang = $course->get_language();
            if (array_key_exists($lang, $arr))
            {
                $arr[$lang][0] ++;
            }
            else
            {
                $arr[$lang][0] = 1;
            }
        }

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