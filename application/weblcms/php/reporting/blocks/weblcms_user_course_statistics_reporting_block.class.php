<?php
namespace application\weblcms;

use common\libraries\Path;

require_once dirname(__FILE__) . '/../weblcms_tool_reporting_block.class.php';
require_once Path :: get_reporting_path() . '/lib/reporting_data.class.php';

class WeblcmsUserCourseStatisticsReportingBlock extends WeblcmsToolReportingBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $course_id = $this->get_course_id();
        $user_id = $this->get_user_id();
        require_once Path :: get_user_path() . 'trackers/visit_tracker.class.php';
        $tracker = new VisitTracker();

        $conditions[] = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*&course=' . $course_id . '*');
        $conditions[] = new EqualityCondition(VisitTracker :: PROPERTY_USER_ID, $user_id);
        $condition = new AndCondition($conditions);
        $trackerdata = $tracker->retrieve_tracker_items($condition);
        $count = 0;

        $firstconnection = null;
        foreach ($trackerdata as $key => $value)
        {
            $count ++;
            if (is_null($firstconnection))
            {
                $firstconnection = $value->get_enter_date();
                $lastconnection = $value->get_leave_date();
            }

            if (self :: greaterDate($value->get_leave_date(), $lastconnection))
                $lastconnection = $value->get_leave_date();
            if (self :: greaterDate($firstconnection, $value->get_enter_date()))
                $firstconnection = $value->get_enter_date();
        }

        $reporting_data->set_categories(array(Translation :: get('FirstAccessToCourse'), Translation :: get('LastAccessToCourse'), Translation :: get('TimeOnCourse'), Translation :: get('TotalTimesAccessed')));
        $reporting_data->set_rows(array(Translation :: get('count')));

        $reporting_data->add_data_category_row(Translation :: get('FirstAccessToCourse'), Translation :: get('count'), DatetimeUtilities :: format_locale_date(null, $firstconnection));
        $reporting_data->add_data_category_row(Translation :: get('LastAccessToCourse'), Translation :: get('count'), DatetimeUtilities :: format_locale_date(null, $lastconnection));
        $reporting_data->add_data_category_row(Translation :: get('TimeOnCourse'), Translation :: get('count'), $this->get_total_time($trackerdata));
        $reporting_data->add_data_category_row(Translation :: get('TotalTimesAccessed'), Translation :: get('count'), $count);

        return $reporting_data;
    }

    public static function greaterDate($start_date, $end_date)
    {
        if ($start_date - $end_date > 0)
            return 1;
        else
            return 0;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_available_displaymodes()
    {
        $modes = array();
        //$modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter :: DISPLAY_TABLE] = Translation :: get('Table');
        return $modes;
    }
}
?>