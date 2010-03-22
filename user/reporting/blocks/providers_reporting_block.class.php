<?php
require_once dirname (__FILE__) . '/../user_reporting_block.class.php';

class ProvidersReportingBlock extends UserReportingBlock
{
	public function count_data()
	{
		require_once (dirname(__FILE__) . '/../trackers/providers_tracker.class.php');
        $tracker = new ProvidersTracker();
        $condition = new EqualityCondition(ProvidersTracker :: PROPERTY_TYPE, 'provider');
        $description[0] = Translation :: get('Providers');

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