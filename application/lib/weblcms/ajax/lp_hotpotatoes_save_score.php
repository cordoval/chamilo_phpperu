<?php
/**
 * $Id: lp_hotpotatoes_save_score.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.ajax
 */
require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once dirname(__FILE__) . '/../trackers/weblcms_lpi_attempt_tracker.class.php';

$id = Request :: post('id');
$score = Request :: post('score');

$dummy = new WeblcmsLpiAttemptTracker();
$condition = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_ID, $id);
$trackers = $dummy->retrieve_tracker_items($condition);
if ($trackers[0])
{
    $trackers[0]->set_score($score);
    $trackers[0]->set_status('completed');
    $trackers[0]->set_total_time($trackers[0]->get_total_time() + (time() - $trackers[0]->get_start_time()));
    $trackers[0]->update();
}

?>