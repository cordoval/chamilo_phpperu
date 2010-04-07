<?php
require_once PATH::get_reporting_path() . '/lib/reporting_data.class.php';
require_once dirname (__FILE__) . '/../weblcms_tool_reporting_block.class.php';

class WeblcmsLastAccessReportingBlock extends WeblcmsToolReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
		
		$course_id = $this->get_course_id();
        $user_id = $this->get_user_id();
        $udm = UserDataManager :: get_instance();

        if (isset($user_id))
        {
            $conditions[] = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*course=' . $course_id . '*');
            $conditions[] = new EqualityCondition(VisitTracker :: PROPERTY_USER_ID, $user_id);
            $condition = new AndCondition($conditions);
        }
        else
        {
            $condition = new PattenMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*&course=' . $course_id . '*');
        }

        $user = $udm->retrieve_user($user_id);
        $arr = self :: visit_tracker_to_array($condition, $user);

        $description['default_sort_column'] = 1;
	
		return $reporting_data;
	}	

	public function retrieve_data()
	{
		return $this->count_data();		
	}
	
	public function get_available_displaymodes()
	{
		$modes = array();
        $modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter::DISPLAY_TABLE] = Translation :: get('Table');
        //$modes[ReportingChartFormatter::DISPLAY_PIE] = Translation :: get('Chart:Pie');
        return $modes;
	}
}
?>