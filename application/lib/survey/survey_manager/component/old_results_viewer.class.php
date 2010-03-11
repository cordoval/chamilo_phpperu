<?php
/**
 * $Id: results_viewer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.survey_manager.component
 */
require_once dirname(__FILE__) . '/../survey_manager.class.php';
require_once dirname(__FILE__) . '/../survey_manager_component.class.php';
require_once dirname(__FILE__) . '/../../trackers/survey_question_attempts_tracker.class.php';
require_once dirname(__FILE__) . '/../../trackers/survey_survey_attempts_tracker.class.php';

/**
 * Component to create a new survey_publication object
 * @author Sven Vanpoucke
 * @author 
 */
class SurveyManagerResultsViewerComponent extends SurveyManagerComponent
{
    private $current_attempt_id;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS)), Translation :: get('BrowseSurveyPublications')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ViewResults')));
        
        $pid = Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION);
        $delete = Request :: get('delete');
        
        if ($delete)
        {
            $split = explode('_', $delete);
            $id = $split[1];
            
            if ($split[0] == 'aid')
            {
                $condition = new EqualityCondition(SurveySurveyAttemptsTracker :: PROPERTY_SURVEY_ID, $id);
            }
            else
            {
                $condition = new EqualityCondition(SurveySurveyAttemptsTracker :: PROPERTY_ID, $id);
                $parameters = array(SurveyManager :: PARAM_SURVEY_PUBLICATION => $pid);
            }
            
            $dummy = new SurveySurveyAttemptsTracker();
            $trackers = $dummy->retrieve_tracker_items($condition);
            foreach ($trackers as $tracker)
            {
                $tracker->delete();
            }
            
            $this->redirect(Translation :: get('SurveyAttemptsDeleted'), false, $parameters);
            exit();
        }
        
        if (! $pid)
        {
            $html = $this->display_summary_results();
        }
        else
        {
            $trail->add(new Breadcrumb($this->get_url(array(SurveyManager :: PARAM_SURVEY_PUBLICATION => $pid)), Translation :: get('ViewSurveyResults')));
            
            $details = Request :: get('details');
            if ($details)
            {
                $trail->add(new Breadcrumb($this->get_url(array(SurveyManager :: PARAM_SURVEY_PUBLICATION => $pid, 'details' => $details)), Translation :: get('ViewSurveyDetails')));
                
                $this->current_attempt_id = $details;
                
                $pub = SurveyDataManager :: get_instance()->retrieve_survey_publication($pid);
                $object = $pub->get_publication_object();
                
                $_GET['display_action'] = 'view_result';
                
                $this->set_parameter('details', $details);
                $this->set_parameter(SurveyManager :: PARAM_SURVEY_PUBLICATION, $pid);
                
                $html = ComplexDisplay :: factory($this, $object->get_type());
                $html->set_root_lo($object);
            }
            else
            {
                $html = $this->display_survey_results($pid);
            }
        }
        
        $this->display_header($trail);
        
        if (is_object($html))
            $html->run();
        else
            echo $html;
        
        $this->display_footer();
    }

    function display_summary_results()
    {
        require_once (Path :: get_application_path() . 'lib/survey/reporting/templates/survey_attempts_summary_template.class.php');
        
        $current_category = Request :: get('category');
        $current_category = $current_category ? $current_category : 0;
        
        $parameters = array('category' => $current_category, 'url' => $this->get_url());
        $template = new SurveyAttemptsSummaryTemplate($this, 0, $parameters, null);
        $template->set_reporting_blocks_function_parameters($parameters);
        return $template->to_html();
    }

    function display_survey_results($pid)
    {
        require_once (Path :: get_application_path() . 'lib/survey/reporting/templates/survey_attempts_template.class.php');
        
        $url = $this->get_url(array(SurveyManager :: PARAM_SURVEY_PUBLICATION => $pid));
        $results_export_url = $this->get_results_exporter_url();
        
        $parameters = array(SurveyManager :: PARAM_SURVEY_PUBLICATION => $pid, 'url' => $url, 'results_export_url' => $results_export_url);
        $template = new SurveyAttemptsTemplate($this, 0, $parameters, null, $pid);
        $template->set_reporting_blocks_function_parameters($parameters);
        return $template->to_html();
    }

    function retrieve_survey_results()
    {
        $condition = new EqualityCondition(SurveyQuestionAttemptsTracker :: PROPERTY_SURVEY_ATTEMPT_ID, $this->current_attempt_id);
        
        $dummy = new SurveyQuestionAttemptsTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        
        $results = array();
        
        foreach ($trackers as $tracker)
        {
            $results[$tracker->get_question_cid()] = array('answer' => $tracker->get_answer(), 'feedback' => $tracker->get_feedback(), 'score' => $tracker->get_score());
        }
        
        return $results;
    }

    function change_answer_data($question_cid, $score, $feedback)
    {
        $conditions[] = new EqualityCondition(SurveyQuestionAttemptsTracker :: PROPERTY_SURVEY_ATTEMPT_ID, $this->current_attempt_id);
        $conditions[] = new EqualityCondition(SurveyQuestionAttemptsTracker :: PROPERTY_QUESTION_CID, $question_cid);
        $condition = new AndCondition($conditions);
        
        $dummy = new SurveyQuestionAttemptsTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        $tracker = $trackers[0];
        $tracker->set_score($score);
        $tracker->set_feedback($feedback);
        $tracker->update();
    }

    function change_total_score($total_score)
    {
        $condition = new EqualityCondition(SurveySurveyAttemptsTracker :: PROPERTY_SURVEY_ID, $this->current_attempt_id);
        $dummy = new SurveySurveyAttemptsTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        $tracker = $trackers[0];
        
        if (! $tracker)
            return;
        
        $tracker->set_total_score($total_score);
        $tracker->update();
    }

    function can_change_answer_data()
    {
        return true;
    }
}
?>