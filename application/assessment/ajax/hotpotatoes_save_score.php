<?php
/**
 * $Id: hotpotatoes_save_score.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.ajax
 */
require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once dirname(__FILE__) . '/../trackers/assessment_assessment_attempts_tracker.class.php';

$id = Request :: post('id');
$score = Request :: post('score');

$dummy = new AssessmentAssessmentAttemptsTracker();
$condition = new EqualityCondition(AssessmentAssessmentAttemptsTracker :: PROPERTY_ID, $id);
$trackers = $dummy->retrieve_tracker_items($condition);
if ($trackers[0])
{
    $trackers[0]->set_total_score($score);
    $trackers[0]->set_status('completed');
    $trackers[0]->set_total_time($trackers[0]->get_total_time() + (time() - $trackers[0]->get_start_time()));
    $trackers[0]->update();
}

?>