<?php
require_once dirname (__FILE__) . '/../user_reporting_block.class.php';

class BrowserReportingBlock extends UserReportingBlock
{
	public function count_data()
	{
		require_once (dirname(__FILE__) . '/../trackers/browsers_tracker.class.php');
        $tracker = new BrowsersTracker();
        $condition = new EqualityCondition(BrowsersTracker :: PROPERTY_TYPE, 'browser');
        $description[0] = Translation :: get('Browsers');

        return Reporting :: array_from_tracker($tracker, $condition, $description);
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
        $modes["Text"] = Translation :: get('Text');
        $modes["Table"] = Translation :: get('Table');
        $modes["Chart:Pie"] = Translation :: get('Chart:Pie');
        $modes["Chart:Bar"] = Translation :: get('Chart:Bar');
        $modes["Chart:Line"] = Translation :: get('Chart:Line');
        $modes["Chart:FilledCubic"] = Translation :: get('Chart:FilledCubic');
        return $modes;
	}
}
?>