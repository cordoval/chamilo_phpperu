<?php

require_once dirname(__FILE__) . '/../user_reporting_block.class.php';

class UserNoOfLoginsHourReportingBlock extends UserReportingBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        require_once (dirname(__FILE__) . '/../../trackers/login_logout_tracker.class.php');
        $conditions = array();
        $conditions[] = new EqualityCondition(LoginLogoutTracker :: PROPERTY_TYPE, 'login');
        $user_id = $this->get_user_id();
        if (isset($user_id))
        {
            $conditions[] = new EqualityCondition(LoginLogoutTracker :: PROPERTY_USER_ID, $user_id);
        }
        $condition = new AndCondition($conditions);

        $data = Tracker :: get_data('login_logout_tracker', UserManager :: APPLICATION_NAME, $condition);

        $hours = UserReportingBlock :: getDateArray($data, 'G');

        $hours_names = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24);
        $reporting_data->set_categories($hours_names);
        $reporting_data->set_rows(array(Translation :: get('logins')));

        foreach ($hours_names as $key => $name)
        {
            $reporting_data->add_data_category_row($name, Translation :: get('logins'), ($hours[$key] ? $hours[$key] : 0));
        }
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    function get_application()
    {
        return UserManager :: APPLICATION_NAME;
    }

    public function get_available_displaymodes()
    {
        $modes = array();
        $modes[ReportingFormatter :: DISPLAY_TABLE] = Translation :: get('Table');
        $modes[ReportingChartFormatter :: DISPLAY_PIE] = Translation :: get('Chart:Pie');
        $modes[ReportingChartFormatter :: DISPLAY_BAR] = Translation :: get('Chart:Bar');
        $modes[ReportingChartFormatter :: DISPLAY_LINE] = Translation :: get('Chart:Line');
        $modes[ReportingChartFormatter :: DISPLAY_FILLED_CUBIC] = Translation :: get('Chart:FilledCubic');
        return $modes;
    }
}
?>