<?php
require_once dirname (__FILE__) . '/../user_reporting_block.class.php';

class UserActiveInactiveReportingBlock extends UserReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
		$udm = UserDataManager :: get_instance();
        $users = $udm->retrieve_users();
        $active[Translation :: get('Active')] = 0;
        $active[Translation :: get('Inactive')] = 0;
        while ($user = $users->next_result())
        {
            if ($user->get_active())
            {
                $active[Translation :: get('Active')]++;
            }
            else
            {
                $active[Translation :: get('Inactive')]++;
            }
        }
        $reporting_data->set_categories(array(Translation :: get('Active'), Translation :: get('Inactive')));
        $reporting_data->set_rows(array(Translation :: get('count')));

        $reporting_data->add_data_category_row(Translation :: get('Active'), Translation :: get('count'), $active[Translation :: get('Active')]);
		$reporting_data->add_data_category_row(Translation :: get('Inactive'), Translation :: get('count'), $active[Translation :: get('Inactive')]);
        return $reporting_data;
	}	
	
	public function retrieve_data()
	{
		return $this->count_data();
	}
	
	public function get_application()
	{
		return UserManager::APPLICATION_NAME;
	}
	
	public function get_available_displaymodes()
	{
		$modes = array();
        $modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter::DISPLAY_TABLE] = Translation :: get('Table');
        $modes[ReportingChartFormatter::DISPLAY_PIE] = Translation :: get('Chart:Pie');
        $modes[ReportingChartFormatter::DISPLAY_BAR] = Translation :: get('Chart:Bar');
        $modes[ReportingChartFormatter::DISPLAY_LINE] = Translation :: get('Chart:Line');
        $modes[ReportingChartFormatter::DISPLAY_FILLED_CUBIC] = Translation :: get('Chart:FilledCubic');
        return $modes;
	}
}
?>