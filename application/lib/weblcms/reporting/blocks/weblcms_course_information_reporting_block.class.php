<?php

require_once dirname (__FILE__) . '/../weblcms_course_reporting_block.class.php';
require_once PATH::get_reporting_path() . '/lib/reporting_data.class.php';
class WeblcmsCourseInformationReportingBlock extends WeblcmsCourseReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
		
		$wdm = WeblcmsDataManager :: get_instance();
        $course = $wdm->retrieve_course($this->get_course_id());
        //$arr[Translation :: get('Name')][] = $course->get_name();
        //$arr[Translation :: get('Titular')][] = $course->get_titular_string();
        
        $reporting_data->set_categories(array(Translation :: get('Name'), Translation :: get('Titular')));
        $reporting_data->set_rows(array(Translation :: get('count')));

        $reporting_data->add_data_category_row(Translation :: get('Name'), Translation :: get('count'), $course->get_name());
        $reporting_data->add_data_category_row(Translation :: get('Titular'), Translation :: get('count'), $course->get_titular_string());
        
        return $reporting_data;
    }	
	
	public function retrieve_data()
	{
		return $this->count_data();		
	}

	
	public function get_available_displaymodes()
	{
		$modes = array();
		//$modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter::DISPLAY_TABLE] = Translation :: get('Table');
        return $modes;
	}
}
?>