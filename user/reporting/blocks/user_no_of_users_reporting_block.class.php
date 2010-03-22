<?php
require_once dirname (__FILE__) . '/../user_reporting_block.class.php';

class UserNoOfUsersReportingBlock extends UserReportingBlock
{
	public function count_data()
	{
		$udm = UserDataManager :: get_instance();

        $arr[Translation :: get('NumberOfUsers')][] = $udm->count_users();

        return Reporting :: getSerieArray($arr);
	}	
	
	public function retrieve_data()
	{
		return count_data();		
	}
	
	function get_application()
	{
		return UserManager::APPLICATION_NAME;
	}
}
?>