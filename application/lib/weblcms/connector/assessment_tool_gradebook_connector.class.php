<?php
require_once dirname(__FILE__) . '/../trackers/weblcms_assessment_attempts_tracker.class.php';
class AssessmentToolGradebookConnector
{
	function AssessmentToolGradebookConnector()
	{
		
	}
	function get_tracker_score($publication_id)
	{
		$dummy = new WeblcmsAssessmentAttemptsTracker();
        $condition = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $publication_id);
        
        $trackers = $dummy->retrieve_tracker_items($condition);
        if(!$trackers)
        	return false;
		for($i=0;$i<count($trackers);$i++)
        {
        	$scores[] = $trackers[$i]->get_total_score();
        }
		return $scores;
	}
	
	function get_tracker_user($publication_id)
	{
		$dummy = new WeblcmsAssessmentAttemptsTracker();
        $condition = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $publication_id);
        
        $trackers = $dummy->retrieve_tracker_items($condition);
        if(!$trackers)
        	return false;
        for($i=0;$i<count($trackers);$i++)
        {
        	$user_ids[] = $trackers[$i]->get_user_id();
        }
        return $user_ids;
	}
	
	function get_tracker_date($publication_id)
	{
		$dummy = new WeblcmsAssessmentAttemptsTracker();
        $condition = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $publication_id);
        
        $trackers = $dummy->retrieve_tracker_items($condition);
        if(!$trackers)
        	return false;
		for($i=0;$i<count($trackers);$i++)
        {
        	$dates[] = $trackers[$i]->get_date();
        }
        return $dates;
	}
//	
//	function get_tracker_id($publication_id)
//	{
//		$dummy = new WeblcmsAssessmentAttemptsTracker();
//        $condition = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $publication_id);
//        
//        $trackers = $dummy->retrieve_tracker_items($condition);
//        if(!$trackers)
//        	return false;
//		for($i=0;$i<count($trackers);$i++)
//        {
//        	$dates[] = $trackers[$i]->get_id();
//        }
//        return $dates;
//	}
}
?>