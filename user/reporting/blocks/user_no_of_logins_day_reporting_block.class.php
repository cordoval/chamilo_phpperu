<?php
require_once dirname (__FILE__) . '/../user_reporting_block.class.php';
require_once PATH::get_reporting_path() . '/lib/reporting_data.class.php';

class  UserNoOfLoginsDayReportingBlock extends UserReportingBlock
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
			$conditions[] = new EqualityCondition(LoginLogoutTracker::PROPERTY_USER_ID, $user_id);
		}
		$condition = new AndCondition($conditions);

		$data = Tracker :: get_data('login_logout_tracker', UserManager :: APPLICATION_NAME, $condition);
//        $tracker = new LoginLogoutTracker();
//        $trackerdata = $tracker->retrieve_tracker_items($condition);

//		dump($data);

        $days = UserReportingBlock :: getDateArray($data, 'N');
        $new_days = array();

        $day_names = array(Translation :: get('MondayLong'), Translation :: get('TuesdayLong'), Translation :: get('WednesdayLong'), Translation :: get('ThursdayLong'), Translation :: get('FridayLong'), Translation :: get('SaturdayLong'), Translation :: get('SundayLong'));

        $reporting_data->set_categories($day_names);
        $reporting_data->set_rows(array(Translation :: get('logins')));

        foreach ($day_names as $key => $name)
        {
            $reporting_data->add_data_category_row($name, Translation :: get('logins'), ($days[$key] ? $days[$key] : 0));
        }
        return $reporting_data;
	}

	public function is_sortable()
	{
		return true;
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
        //$modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter::DISPLAY_TABLE] = Translation :: get('Table');
        $modes[ReportingChartFormatter::DISPLAY_BAR] = Translation :: get('Chart:Bar');
        $modes[ReportingChartFormatter::DISPLAY_LINE] = Translation :: get('Chart:Line');
        $modes[ReportingChartFormatter::DISPLAY_FILLED_CUBIC] = Translation :: get('Chart:FilledCubic');
        return $modes;
	}
}
?>