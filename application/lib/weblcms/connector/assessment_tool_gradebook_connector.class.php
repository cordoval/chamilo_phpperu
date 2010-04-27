<?php
require_once dirname(__FILE__) . '/../trackers/weblcms_assessment_attempts_tracker.class.php';
class AssessmentToolGradebookConnector
{
	function AssessmentToolGradebookConnector()
	{
		
	}
	function get_tracker_score($application, $publication_id)
	{
		$dummy = new WeblcmsAssessmentAttemptsTracker();
        $condition = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $publication_id);
        
        $trackers = $dummy->retrieve_tracker_items($condition);
        if(!$trackers)
        	return false;
		return $trackers[0]->get_total_score();
	}
	
	function get_tracker_user($application, $publication_id)
	{
		$dummy = new WeblcmsAssessmentAttemptsTracker();
        $condition = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $publication_id);
        
        $trackers = $dummy->retrieve_tracker_items($condition);
        if(!$trackers)
        	return false;
        return $trackers[0]->get_user_id();
	}
}
?>