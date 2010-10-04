<?php
require_once dirname (__FILE__) . '/../user_reporting_block.class.php';

class UserNoOfLoginsReportingBlock extends UserReportingBlock
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

        $count = Tracker :: count_data('login_logout_tracker', UserManager :: APPLICATION_NAME, $condition);

		$reporting_data->set_categories(array(Translation :: get('logins')));
        $reporting_data->set_rows(array(Translation :: get('count')));

        $reporting_data->add_data_category_row(Translation :: get('logins'), Translation :: get('count'), $count);

        return $reporting_data;
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

        return $modes;
	}
}
?>