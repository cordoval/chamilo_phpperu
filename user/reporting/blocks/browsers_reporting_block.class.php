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
		return count_data();		
	}
	
	function get_application()
	{
		return UserManager::APPLICATION_NAME;
	}
}
?>