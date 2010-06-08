<?php
require_once dirname (__FILE__) . '/../user_reporting_block.class.php';

class UserNoOfUsersPictureReportingBlock extends UserReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
		$udm = UserDataManager :: get_instance();
        $users = $udm->retrieve_users();
        $picturetext = Translation :: get('Picture');
        $nopicturetext = Translation :: get('NoPicture');
        $picture[$picturetext] = 0;
        $picture[$nopicturetext] = 0;

        while ($user = $users->next_result())
        {
            if ($user->get_picture_uri())
            {
                $picture[$picturetext] ++;
            }
            else
            {
                $picture[$nopicturetext] ++;
            }
        }
        
        $reporting_data->set_categories(array(Translation :: get('Picture'), Translation :: get('NoPicture')));
        $reporting_data->set_rows(array(Translation :: get('count')));

        $reporting_data->add_data_category_row(Translation :: get('Picture'), Translation :: get('count'),$picture[$picturetext]);
		$reporting_data->add_data_category_row(Translation :: get('NoPicture'), Translation :: get('count'),$picture[$nopicturetext]);
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
        $modes[ReportingChartFormatter::DISPLAY_PIE] = Translation :: get('Chart:Pie'); 
        $modes[ReportingChartFormatter::DISPLAY_BAR] = Translation :: get('Chart:Bar');
        $modes[ReportingChartFormatter::DISPLAY_LINE] = Translation :: get('Chart:Line');
        $modes[ReportingChartFormatter::DISPLAY_FILLED_CUBIC] = Translation :: get('Chart:FilledCubic');
        return $modes;
	}
}
?>