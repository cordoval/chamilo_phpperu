<?php
/**
 * $Id: set_value.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.javascript.scorm.ajax
 */
require_once dirname(__FILE__) . '/../../../../../../../../common/global.inc.php';
require_once Path :: get_application_path() . 'lib/weblcms/trackers/weblcms_lpi_attempt_tracker.class.php';

$tracker_id = Request :: post('tracker_id');
$variable = Request :: post('variable');
$value = Request :: post('value');

$condition = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_ID, $tracker_id);
$dummy = new WeblcmsLpiAttemptTracker();
$trackers = $dummy->retrieve_tracker_items($condition);
$tracker = $trackers[0];

$rdm = RepositoryDataManager :: get_instance();
$item = $rdm->retrieve_complex_content_object_item($tracker->get_lp_item_id());

$learning_path_item = $rdm->retrieve_content_object($item->get_ref());
$scorm_item = $rdm->retrieve_content_object($learning_path_item->get_reference());

switch ($variable)
{
    case 'cmi.success_status' :
        $tracker->set_status('completed');
        $tracker->set_total_time($tracker->get_total_time() + (time() - $tracker->get_start_time()));
        $tracker->update();
        
        $objs = $scorm_item->get_objectives();
        if ($objs)
        {
            $objectives = $objs->get_objectives();
            
            foreach ($objectives as $index => $objective)
            {
                if ($objective && $objective->get_contributes_to_rollup())
                {
                    $parameters = array('lpi_view_id' => $tracker->get_id(), 'objective_id' => $objective->get_id(), 'status' => 'completed', 'display_order' => $index);
                    Events :: trigger_event('attempt_learning_path_item_objective', 'weblcms', $parameters);
                }
            }
        }
        
        break;
    case 'cmi.completion_status' :
        $tracker->set_status($value);
        $tracker->update();
        break;
    case 'cmi.core.lesson_status' :
        if ($value == 'completed')
        {
            $mastery_score = $learning_path_item->get_mastery_score();
            if ($mastery_score > $tracker->get_score())
            {
                $value = 'failed';
            }
            else
            {
                $value = 'passed';
            }
        }
        $tracker->set_status($value);
        $tracker->update();
        break;
    case 'cmi.core.lesson_location' :
        $tracker->set_lesson_location($value);
        $tracker->update();
        break;
    case 'cmi.suspend_data' :
        $tracker->set_suspend_data($value);
        $tracker->update();
        break;
    case 'cmi.core.session_time' :
        list($h, $m, $s) = explode(':', $value);
        $s = explode('.', $s);
        $s = $s[0];
        $total_seconds = ($h * 3600) + ($m * 60) + ($s);
        $tracker->set_total_time($tracker->get_total_time() + $total_seconds);
        $tracker->update();
        break;
    case 'cmi.core.score.raw' :
        $tracker->set_score($value);
        $tracker->update();
        break;
    case 'cmi.core.score.max' :
        $tracker->set_max_score($value);
        $tracker->update();
        break;
    case 'cmi.core.score.min' :
        $tracker->set_min_score($value);
        $tracker->update();
        break;
}

?>