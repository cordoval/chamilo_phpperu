<?php
require_once dirname (__FILE__) . '/../weblcms_course_reporting_block.class.php';

class WeblcmsNoOfUsersSubscribedCourseReportingBlock extends WeblcmsCourseReportingBlock
{
	public function count_data()
	{
        $reporting_data = new ReportingData();
		require_once Path :: get_application_path() . '/lib/weblcms/weblcms_data_manager.class.php';
        $udm = UserDataManager :: get_instance();
        $users = $udm->count_users();

        $wdm = WeblcmsDataManager :: get_instance();
        $courses = $wdm->count_distinct_course_user_relations();

        $reporting_data->set_categories(array(Translation :: get('UsersSubscribedToCourse'), Translation :: get('UsersNotSubscribedToCourse')));
        $reporting_data->set_rows(array(Translation :: get('count')));

        $reporting_data->add_data_category_row(Translation :: get('UsersSubscribedToCourse'), Translation :: get('count'), $courses);
		$reporting_data->add_data_category_row(Translation :: get('UsersNotSubscribedToCourse'), Translation :: get('count'), $users - $courses);
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
		$modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter::DISPLAY_TABLE] = Translation :: get('Table');
     	$modes[ReportingChartFormatter::DISPLAY_PIE] = Translation :: get('Chart:Pie');
        $modes[ReportingChartFormatter::DISPLAY_BAR] = Translation :: get('Chart:Bar');
        $modes[ReportingChartFormatter::DISPLAY_LINE] = Translation :: get('Chart:Line');
        $modes[ReportingChartFormatter::DISPLAY_FILLED_CUBIC] = Translation :: get('Chart:FilledCubic');  
        return $modes;
	}
}
?>