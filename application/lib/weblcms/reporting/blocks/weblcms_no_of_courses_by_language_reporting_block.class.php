<?php

require_once dirname (__FILE__) . '/../weblcms_course_reporting_block.class.php';
require_once PATH::get_reporting_path() . '/lib/reporting_data.class.php';

class WeblcmsNoOfCoursesByLanguageReportingBlock extends WeblcmsCourseReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
		$wdm = WeblcmsDataManager :: get_instance();
        $arr = array();
        $courses = $wdm->retrieve_courses();
        while ($course = $courses->next_result())
        {
            //$lang = $course->get_language();
            if (/*array_key_exists($lang, $arr*/$lang = 'english')
            {
                $arr[$lang] ++;
            }
            else
            {
                $arr[$lang] = 1;
            }
        }
        $reporting_data->set_categories(array('english'));
        $reporting_data->set_rows(array(Translation :: get('count')));

        $reporting_data->add_data_category_row('english', Translation :: get('count'), $arr['english']);
        return $reporting_data;
	}	
	
	public function retrieve_data()
	{
		return $this->count_data();		
	}
	
	function get_application()
	{
		return WeblcmsManager::APPLICATION_NAME;
	}
	
	public function get_available_displaymodes()
	{
		$modes = array();
        //$modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter::DISPLAY_TABLE] = Translation :: get('Table');
        $modes[ReportingChartFormatter::DISPLAY_PIE] = Translation :: get('Chart:Pie');
        return $modes;
	}
}
?>