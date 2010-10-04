<?php
require_once dirname (__FILE__) . '/../user_reporting_block.class.php';

class BrowsersReportingBlock extends UserReportingBlock
{
	public function count_data()
	{
		$reporting_data = new ReportingData();
		require_once (dirname(__FILE__) . '/../../trackers/browsers_tracker.class.php');
        $tracker = new BrowsersTracker();
        $condition = new EqualityCondition(BrowsersTracker :: PROPERTY_TYPE, 'browser');
        $description[0] = Translation :: get('Browsers');
        $data = Reporting :: array_from_tracker($tracker, $condition, $description);
        $keys = array_keys($data);
        $reporting_data->set_categories($keys);
        $reporting_data->set_rows(array(Translation :: get('Browsers')));
        
        foreach ($keys as $key => $name)
        {
            $reporting_data->add_data_category_row($name, Translation :: get('Browsers'), $data[$name]);
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