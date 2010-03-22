<?php
require_once dirname (__FILE__) . '/../user_reporting_block.class.php';

class CountriesReportingBlock extends UserReportingBlock
{
	public function count_data()
	{
		require_once (dirname(__FILE__) . '/../trackers/countries_tracker.class.php');
        $tracker = new CountriesTracker();
        $condition = new EqualityCondition(CountriesTracker :: PROPERTY_TYPE, 'country');
        $description[0] = Translation :: get('Countries');

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