<?php
/**
 * $Id: results_viewer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component
 */
require_once dirname(__FILE__) . '/../assessment_manager.class.php';
require_once dirname(__FILE__) . '/../../trackers/assessment_question_attempts_tracker.class.php';
require_once dirname(__FILE__) . '/../../trackers/assessment_assessment_attempts_tracker.class.php';

/**
 * Component to create a new assessment_publication object
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerResultsViewerComponent extends AssessmentManager
{
    private $current_attempt_id;
    private $trail;
    private $object;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->trail = $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowseAssessmentPublications')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ViewResults')));
        
        $pid = Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION);
        $delete = Request :: get('delete');
        
        if ($delete)
        {
            $split = explode('_', $delete);
            $id = $split[1];
            
            if ($split[0] == 'aid')
            {
                $condition = new EqualityCondition(AssessmentAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $id);
            }
            else
            {
                $condition = new EqualityCondition(AssessmentAssessmentAttemptsTracker :: PROPERTY_ID, $id);
                $parameters = array(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION => $pid);
            }
            
            $dummy = new AssessmentAssessmentAttemptsTracker();
            $trackers = $dummy->retrieve_tracker_items($condition);
            foreach ($trackers as $tracker)
            {
                $tracker->delete();
            }
            
            $this->redirect(Translation :: get('AssessmentAttemptsDeleted'), false, $parameters);
            exit();
        }
        
        if (! $pid)
        {
            echo $this->display_summary_results();
        }
        else
        {
            $trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION => $pid)), Translation :: get('ViewAssessmentResults')));
            
            $details = Request :: get('details');
            if ($details)
            {
                $trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION => $pid, 'details' => $details)), Translation :: get('ViewAssessmentDetails')));
                
                $this->current_attempt_id = $details;
                
                $pub = AssessmentDataManager :: get_instance()->retrieve_assessment_publication($pid);
                $object = $pub->get_publication_object();
                
                $_GET['display_action'] = 'view_result';
                
                $this->set_parameter('details', $details);
                $this->set_parameter(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION, $pid);
                
                $this->object = $object;
                ComplexDisplay :: launch($object->get_type(), $this);
            }
            else
            {
                $this->display_header();
                echo $this->display_assessment_results($pid);
                $this->display_footer();
            }
        }
    }

    function get_root_content_object()
    {
        return $this->object;
    }

    function display_header($trail)
    {
        if ($trail)
        {
            $this->trail->merge($trail);
        }
        
        parent :: display_header($this->trail);
    }

    function display_summary_results()
    {
        require_once (Path :: get_application_path() . 'lib/assessment/reporting/templates/assessment_attempts_summary_template.class.php');
        
        $current_category = Request :: get('category');
        $current_category = $current_category ? $current_category : 0;
        $parameters = array('category' => $current_category, 'url' => $this->get_url());
        $database = ReportingDataManager :: get_instance();
        $template_obj = $database->retrieve_reporting_template_object('assessment_attempts_summary_template');
        $template = new AssessmentAttemptsSummaryTemplate($this, $template_obj->get_id(), $parameters, null);
        //$template->set_reporting_blocks_function_parameters($parameters);
        return $template->to_html();
    }

    function display_assessment_results($pid)
    {
        require_once (Path :: get_application_path() . 'lib/assessment/reporting/templates/assessment_attempts_template.class.php');
        
        $url = $this->get_url(array(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION => $pid));
        $results_export_url = $this->get_results_exporter_url();
        $parameters = array(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION => $pid, 'url' => $url, 'results_export_url' => $results_export_url);
        $database = ReportingDataManager :: get_instance();
        $template_obj = $database->retrieve_reporting_template_object('assessment_attempts_template');
        $template = new AssessmentAttemptsTemplate($this, $template_obj->get_id(), $parameters, null, $pid);
        //$template->set_reporting_blocks_function_parameters($parameters);
        return $template->to_html();
    }

    function retrieve_assessment_results()
    {
        $condition = new EqualityCondition(AssessmentQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID, $this->current_attempt_id);
        
        $dummy = new AssessmentQuestionAttemptsTracker();
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
        $conditions[] = new EqualityCondition(AssessmentQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID, $this->current_attempt_id);
        $conditions[] = new EqualityCondition(AssessmentQuestionAttemptsTracker :: PROPERTY_QUESTION_CID, $question_cid);
        $condition = new AndCondition($conditions);
        
        $dummy = new AssessmentQuestionAttemptsTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        $tracker = $trackers[0];
        $tracker->set_score($score);
        $tracker->set_feedback($feedback);
        $tracker->update();
    }

    function change_total_score($total_score)
    {
        $condition = new EqualityCondition(AssessmentAssessmentAttemptsTracker :: PROPERTY_ID, $this->current_attempt_id);
        $dummy = new AssessmentAssessmentAttemptsTracker();
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