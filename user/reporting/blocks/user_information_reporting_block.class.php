<?php

require_once dirname (__FILE__) . '/../user_reporting_block.class.php';

class UserInformationReportingBlock extends UserReportingBlock
{
	public function count_data()
	{

		$uid = $params[ReportingManager :: PARAM_USER_ID];
        //$uid = 2;
        require_once Path :: get_admin_path() . '/trackers/online_tracker.class.php';
        $udm = UserDataManager :: get_instance();
        $tracking = new OnlineTracker();

        $items = $tracking->retrieve_tracker_items();
        foreach ($items as $item)
        {
            if ($item->get_user_id() == $uid)
            {
                $online = 1;
            }
        }

        $user = $udm->retrieve_user($uid);

        $arr[Translation :: get('Name')][] = $user->get_fullname();
        $arr[Translation :: get('Email')][] = '<a href="mailto:' . $user->get_email() . '" >' . $user->get_email() . '</a>';
        $arr[Translation :: get('Phone')][] = $user->get_phone();
        //$arr[Translation :: get('Status')] = $user->get_status_name();
        $arr[Translation :: get('Online')][] = ($online) ? Translation :: get('Online') : Translation :: get('Offline');

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
		
	}
}
?>