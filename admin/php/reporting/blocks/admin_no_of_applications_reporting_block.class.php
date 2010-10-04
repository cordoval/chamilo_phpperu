<?php
require_once dirname (__FILE__) . '/../admin_reporting_block.class.php';
require_once PATH :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class AdminNoOfApplicationsReportingBlock extends AdminReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
		$user = UserDataManager :: get_instance()->retrieve_user(Session :: get_user_id());
        $adm = new AdminManager($user);
        $arr[Translation :: get('NumberOfApplications')] = 0;
        foreach ($adm->get_application_platform_admin_links() as $application_links)
        {
            $arr[Translation :: get('NumberOfApplications')] ++;
        }
		
        $reporting_data->set_categories(array(Translation :: get('NumberOfApplications')));
        $reporting_data->set_rows(array(Translation :: get('count')));

        $reporting_data->add_data_category_row(Translation :: get('NumberOfApplications'), Translation :: get('count'), $arr[Translation :: get('NumberOfApplications')]);
        return $reporting_data;
	}	
	
	public function retrieve_data()
	{
		return $this->count_data();
	}
	
	public function get_application()
	{
		return AdminManager::APPLICATION_NAME;
	}
	
	public function get_available_displaymodes()
	{
		$modes = array();
        //$modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        return $modes;
	}
}
?>