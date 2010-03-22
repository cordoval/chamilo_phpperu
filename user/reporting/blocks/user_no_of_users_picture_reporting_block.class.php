<?php
require_once dirname (__FILE__) . '/../user_reporting_block.class.php';

class UserNoOfUsersPictureReportingBlock extends UserReportingBlock
{
	public function count_data()
	{
		$udm = UserDataManager :: get_instance();
        $users = $udm->retrieve_users();
        $picturetext = Translation :: get('Picture');
        $nopicturetext = Translation :: get('NoPicture');
        $picture[$picturetext][0] = 0;
        $picture[$nopicturetext][0] = 0;
        while ($user = $users->next_result())
        {
            if ($user->get_picture_uri())
            {
                $picture[$picturetext][0] ++;
            }
            else
            {
                $picture[$nopicturetext][0] ++;
            }
        }
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