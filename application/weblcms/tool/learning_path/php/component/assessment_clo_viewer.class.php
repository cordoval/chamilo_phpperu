<?php
namespace application\weblcms\tool\learning_path;

use common\libraries\Path;

/**
 * $Id: learning_path_assessment_clo_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component
 */
require_once Path :: get_application_path() . 'lib/weblcms/trackers/weblcms_lpi_attempt_tracker.class.php';

class LearningPathToolAssessmentCloViewerComponent extends LearningPathTool
{
    private $lpi_attempt_id;
    private $object;

    function run()
    {
        $assessment = Request :: get('oid');
        $lpi_attempt_id = Request :: get('lpi_attempt_id');
        
        $object = RepositoryDataManager :: get_instance()->retrieve_content_object($assessment);
        $this->object = $object;
        
        $this->lpi_attempt_id = $lpi_attempt_id;
        
        $this->set_parameter(LearningPathTool :: PARAM_ACTION, LearningPathTool :: ACTION_VIEW_ASSESSMENT_CLO);
        $this->set_parameter('oid', $assessment);
        $this->set_parameter('lpi_attempt_id', $lpi_attempt_id);
        $this->set_parameter('cid', Request :: get('cid'));
        
        ComplexDisplay :: launch($object->get_type(), $this);
    }

    function get_root_content_object()
    {
        return $this->object;
    }

    function display_header($trail)
    {
        return Display :: small_header();
    }

    function display_footer()
    {
        return null;
    }

    function save_answer($complex_question_id, $answer, $score)
    {
        $parameters = array();
        $parameters[WeblcmsLearningPathQuestionAttemptsTracker :: PROPERTY_LPI_ATTEMPT_ID] = $this->lpi_attempt_id;
        $parameters[WeblcmsLearningPathQuestionAttemptsTracker :: PROPERTY_QUESTION_CID] = $complex_question_id;
        $parameters[WeblcmsLearningPathQuestionAttemptsTracker :: PROPERTY_ANSWER] = $answer;
        $parameters[WeblcmsLearningPathQuestionAttemptsTracker :: PROPERTY_SCORE] = $score;
        $parameters[WeblcmsLearningPathQuestionAttemptsTracker :: PROPERTY_FEEDBACK] = '';
        
        Event :: trigger('attempt_learning_path_question', WeblcmsManager :: APPLICATION_NAME, $parameters);
    }

    function finish_assessment($total_score)
    {
        $condition = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_ID, $this->lpi_attempt_id);
        
        $dummy = new WeblcmsLpiAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        $lpi_tracker = $trackers[0];
        
        $lpi_tracker->set_score($total_score);
        $lpi_tracker->set_total_time($lpi_tracker->get_total_time() + (time() - $lpi_tracker->get_start_time()));
        
        $cloi = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item(Request :: get('cid'));
        $lp_item = RepositoryDataManager :: get_instance()->retrieve_content_object($cloi->get_ref());
        $mastery_score = $lp_item->get_mastery_score();
        
        if ($mastery_score)
        {
            $status = ($total_score >= $mastery_score) ? 'passed' : 'failed';
        }
        else
        {
            $status = 'completed';
        }
        
        $lpi_tracker->set_status($status);
        $lpi_tracker->update();
    }

    function get_current_attempt_id()
    {
        return $this->lpi_attempt_id;
    }

    function get_go_back_url()
    {
        return null;
    }

}
?>