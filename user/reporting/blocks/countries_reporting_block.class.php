<?php
require_once dirname (__FILE__) . '/../user_reporting_block.class.php';

class CountriesReportingBlock extends UserReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
		require_once (dirname(__FILE__) . '/../../trackers/countries_tracker.class.php');
        $tracker = new CountriesTracker();
        $condition = new EqualityCondition(CountriesTracker :: PROPERTY_TYPE, 'country');
        $description[0] = Translation :: get('Countries');

        $data = Reporting :: array_from_tracker($tracker, $condition, $description);
        $keys = array_keys($data);
        $reporting_data->set_categories($keys);
        $reporting_data->set_rows(array(Translation :: get('Countries')));
        
        foreach ($keys as $key => $name)
        {
            $reporting_data->add_data_category_row($name, Translation :: get('Countries'), $data[$name]);
        }
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
		return $modes;
	}
}
?>