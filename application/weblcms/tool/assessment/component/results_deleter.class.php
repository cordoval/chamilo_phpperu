<?php
/**
 * $Id: assessment_results_deleter.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component
 */
require_once dirname(__FILE__) . '/../../../trackers/weblcms_assessment_attempts_tracker.class.php';
require_once dirname(__FILE__) . '/../../../trackers/weblcms_question_attempts_tracker.class.php';

class AssessmentToolResultsDeleterComponent extends AssessmentTool
{

    function run()
    {
        if (! $this->is_allowed(WeblcmsRights :: DELETE_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        if (Request :: get(AssessmentTool :: PARAM_USER_ASSESSMENT))
        {
            $uaid = Request :: get(AssessmentTool :: PARAM_USER_ASSESSMENT);
            $track = new WeblcmsAssessmentAttemptsTracker();
            $condition = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ID, $uaid);
            $items = $track->retrieve_tracker_items($condition);
            
            if ($items[0] != null)
            {
                $redirect_aid = $items[0]->get_assessment_id();
            }
            $this->delete_user_assessment_results($items[0]);
        }
        
        if (Request :: get(AssessmentTool :: PARAM_ASSESSMENT))
        {
            $aid = Request :: get(AssessmentTool :: PARAM_ASSESSMENT);
            $redirect_aid = $aid;
            $this->delete_assessment_results($aid);
        }
        
        $params = array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS);
        if (isset($redirect_aid))
        {
            $params[AssessmentTool :: PARAM_ASSESSMENT] = $redirect_aid;
        }
        $this->redirect(Translation :: get('ResultsDeleted'), false, $params);
    }

    function delete_user_assessment_results($user_assessment)
    {
        if ($user_assessment != null)
        {
            $track = new WeblcmsQuestionAttemptsTracker();
            $condition = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID, $user_assessment->get_id());
            $items = $track->retrieve_tracker_items();
            
            foreach ($items as $question_attempt)
            {
                $question_attempt->delete();
            }
            $user_assessment->delete();
        }
    }

    function delete_assessment_results($aid)
    {
        $track = new WeblcmsAssessmentAttemptsTracker();
        $condition = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $aid);
        $items = $track->retrieve_tracker_items($condition);
        foreach ($items as $assessment_attempt)
        {
            $this->delete_user_assessment_results($assessment_attempt);
        }
    }
}
?>