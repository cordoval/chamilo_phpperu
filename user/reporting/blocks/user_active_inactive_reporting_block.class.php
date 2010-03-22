<?php
require_once dirname (__FILE__) . '/../user_reporting_block.class.php';

class UserActiveInactiveReportingBlock extends UserReportingBlock
{
	public function count_data()
	{
		/*$udm = UserDataManager :: get_instance();
        $users = $udm->retrieve_users();
        $active[Translation :: get('Active')][0] = 0;
        $active[Translation :: get('Inactive')][0] = 0;
        while ($user = $users->next_result())
        {
            if ($user->get_active())
            {
                $active[Translation :: get('Active')][0] ++;
            }
            else
            {
                $active[Translation :: get('Inactive')][0] ++;
            }
        }
        return Reporting :: getSerieArray($active);¨*/
	}	
	
	public function retrieve_data()
	{
		//return count_data();
		return "test active - inactive";		
	}
	
	function get_application()
	{
		return UserManager::APPLICATION_NAME;
	}
}
?>