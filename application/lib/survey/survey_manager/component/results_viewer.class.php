<?php
/**
 * $Id: results_viewer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.survey_manager.component
 */
require_once dirname(__FILE__) . '/../survey_manager.class.php';

require_once dirname(__FILE__) . '/../../reporting/reporting_survey.class.php';

require_once dirname(__FILE__) . '/../../trackers/survey_question_answer_tracker.class.php';
require_once dirname(__FILE__) . '/../../trackers/survey_participant_tracker.class.php';
require_once PATH :: get_application_path() . '/lib/survey/reporting/templates/survey_attempt_reporting_template.class.php';
//require_once (Path :: get_application_path() . 'lib/survey/reporting/templates/survey_participation_summary_template.class.php');
//require_once (Path :: get_application_path() . 'lib/survey/reporting/templates/survey_question_answers_template.class.php');
//require_once (Path :: get_application_path() . 'lib/survey/reporting/templates/survey_question_results_template.class.php');

/**
 * Component to create a new survey_publication object
 * @author Sven Vanpoucke
 * @author 
 */
class SurveyManagerResultsViewerComponent extends SurveyManager
{
    
    private $question_id;
    private $trail;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->trail = $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS)), Translation :: get('BrowseSurveyPublications')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ViewResults')));
        
        $pid = Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION);
        //$delete = Request :: get('delete');
        
//        if ($delete)
//        {
//            $split = explode('_', $delete);
//            $id = $split[1];
//            
//            if ($split[0] == 'aid')
//            {
//                $condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_ID, $id);
//            }
//            else
//            {
//                $condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_ID, $id);
//                $parameters = array(SurveyManager :: PARAM_SURVEY_PUBLICATION => $pid);
//            }
//            
//            $dummy = new SurveySurveyAttemptsTracker();
//            $trackers = $dummy->retrieve_tracker_items($condition);
//            foreach ($trackers as $tracker)
//            {
//                $tracker->delete();
//            }
//            
//            $this->redirect(Translation :: get('SurveyAttemptsDeleted'), false, $parameters);
//            exit();
//        }
        
        if (! $pid)
        {
            $html = $this->display_summary_results();
        }
        else
        {
            $trail->add(new Breadcrumb($this->get_url(array(SurveyManager :: PARAM_SURVEY_PUBLICATION => $pid)), Translation :: get('ViewSurveyResults')));
            
            $question_id = Request :: get(SurveyManager :: PARAM_SURVEY_QUESTION);
            
            if ($question_id)
            {
                $trail->add(new Breadcrumb($this->get_url(array(SurveyManager :: PARAM_SURVEY_PUBLICATION => $pid, SurveyManager :: PARAM_SURVEY_QUESTION => $question_id)), Translation :: get('ViewSurveyDetails')));
                
                $this->question_id = $question_id;
                $html = $this->display_question_results($pid);
                
            //                $pub = SurveyDataManager :: get_instance()->retrieve_survey_publication($pid);
            //                             
            //                $object = $pub->get_publication_object();
            

            //                $_GET['display_action'] = 'view_result';
            //                
            //                $this->set_parameter('details', $details);
            //                $this->set_parameter(SurveyManager :: PARAM_SURVEY_PUBLICATION, $pid);
            //                
            //                $html = ComplexDisplay :: factory($this, $object->get_type());
            //                $html->set_root_lo($object);
            }
            else
            {
                $html = $this->display_survey_questions($pid);
            }
        }
        
        if (is_object($html))
        {
            $html->run();
        }
        else 
        {
            $this->display_header();
        	echo $html;
        	$this->display_footer();
        }
    }
    
	function display_header($trail)
    {
    	if($trail)
    	{
    		$this->trail->merge($trail);
    	}
    	
    	return parent :: display_header($this->trail);
    }

    function display_summary_results()
    {
        
        $current_category = Request :: get('category');
        $current_category = $current_category ? $current_category : 0;
        $parameters = array();
        $parameters[ReportingSurvey :: PARAM_SURVEY_CATEGORY] = $current_category;
        $parameters[ReportingSurvey :: PARAM_SURVEY_URL] =  $this->get_url();
        $parameters[ReportingSurvey :: PARAM_SURVEY_PARTICIPANT] = $this->get_user_id();
        $template = new SurveyAttemptReportingTemplate($this);
        //$template->set_reporting_blocks_function_parameters($parameters);
        return $template->to_html();
    }

    function display_survey_questions($pid)
    {
        
        $url = $this->get_url(array(SurveyManager :: PARAM_SURVEY_PUBLICATION => $pid));
        $results_export_url = $this->get_results_exporter_url();
        $user_id = $this->get_user_id();
        
        $parameters = array(SurveyManager :: PARAM_SURVEY_PUBLICATION => $pid/*, 'url' => $url, 'results_export_url' => $results_export_url, 'user_id' => $user_id*/);
        $template = new SurveyAttemptReportingTemplate($this);
        $template->set_parameters($parameters);
        //$template->set_reporting_blocks_function_parameters($parameters);
        return $template->to_html();
    }

    function display_question_results($pid)
    {
        
        $url = $this->get_url(array(SurveyManager :: PARAM_SURVEY_PUBLICATION => $pid));
        $results_export_url = $this->get_results_exporter_url();
        
        $parameters = array(SurveyManager :: PARAM_SURVEY_PUBLICATION => $pid, 'url' => $url, 'results_export_url' => $results_export_url, SurveyManager :: PARAM_SURVEY_QUESTION => $this->question_id);
        $template = new SurveyQuestionResultsTemplate($this, 0, $parameters, null, $pid);
        $template->set_reporting_blocks_function_parameters($parameters);
        return $template->to_html();
    
    }

}
?>