<?php
/**
 * $Id: survey_taker.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component
 */

require_once Path :: get_repository_path() . 'lib/content_object/survey/survey.class.php';
//require_once dirname(__FILE__) . '/../survey_invitation.class.php';
require_once Path :: get_application_path() . 'lib/survey/trackers/survey_participant_tracker.class.php';
require_once Path :: get_application_path() . 'lib/survey/trackers/survey_question_answer_tracker.class.php';
require_once Path :: get_application_path() . 'lib/survey/survey_menu.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/survey/context_data_manager/context_data_manager.class.php';

class SurveyToolTakerComponent extends SurveyToolComponent
{

    private $datamanager;

    private $pub;
    private $survey;
    private $pages;
    private $questions;
    private $pid;
    private $participant_id;
    private $active_tracker;
    private $with_menu;
    private $trackers;
    private $trail;

    function run()
    {

        // Retrieving survey
        $this->datamanager = SurveyDataManager :: get_instance();

        if (Request :: get(SurveyManager :: PARAM_SURVEY_PARTICIPANT))
        {
            $this->participant_id = Request :: get(SurveyManager :: PARAM_SURVEY_PARTICIPANT);
            $track = new SurveyParticipantTracker();
            $condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_ID, $this->participant_id);
            $trackers = $track->retrieve_tracker_items($condition);
            $this->active_tracker = $trackers[0];

            $this->set_parameter(SurveyManager :: PARAM_SURVEY_PARTICIPANT, $this->participant_id);
            $this->set_publication_variables($this->active_tracker->get_survey_publication_id());

            $track = new SurveyParticipantTracker();
            $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $this->pid);
            $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $this->active_tracker->get_user_id());
            $condition = new AndCondition($conditions);
                      
            $this->trackers = $track->retrieve_tracker_items($condition);  
        }
        else
        {
            if (Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION))
            {

                $this->set_publication_variables(Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION));

                $track = new SurveyParticipantTracker();
                $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $this->pid);
                $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $this->get_user_id());
                $condition = new AndCondition($conditions);
                $this->trackers = $track->retrieve_tracker_items($condition);

                if (count($this->trackers) === 0)
                {
                    $this->not_allowed($trail, false);
                }

                else
                {
                    $this->active_tracker = $this->trackers[0];
                    $this->set_parameter(SurveyManager :: PARAM_SURVEY_PARTICIPANT, $this->active_tracker->get_id());
                }
            }
        }

        $this->trail = new BreadcrumbTrail();
        if ($this->pub->is_test())
        {
            $this->trail->add(new Breadcrumb($this->get_testcase_url(), Translation :: get('BrowseTestCaseSurveyPublications')));
            $this->trail->add(new Breadcrumb($this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_TESTCASES, TestcaseManager :: PARAM_ACTION => TestcaseManager :: ACTION_BROWSE_SURVEY_PARTICIPANTS, TestcaseManager :: PARAM_SURVEY_PUBLICATION => $this->pid)), Translation :: get('BrowseTestCaseSurveyParticipants')));

        }
        else
        {
            $this->trail->add(new Breadcrumb($this->get_browse_survey_publications_url(), Translation :: get('BrowseSurveyPublications')));

        }
        $this->trail->add(new Breadcrumb($this->get_url(array(SurveyManager :: PARAM_SURVEY_PUBLICATION => $this->pid)), Translation :: get('TakeSurvey')));

        if ($this->pub && ! $this->pub->is_visible_for_target_user($this->get_user()))
        {
            $this->not_allowed($trail, false);
        }

        $db = SurveyContextDataManager :: get_instance();
        $context = $db->retrieve_survey_context_by_id($this->active_tracker->get_context_id());
             
        $this->survey->set_context_instance($context);

        $this->get_survey_html();
    }

    function display_header($trail)
    {
    	if($trail)
    	{
    		$this->trail->merge($trail);
    	}

    	parent :: display_header($this->trail);
    	
        if (count($this->trackers) > 1)
        {
            $this->with_menu = true;
        	echo $this->get_menu_html();
        }
        
        if ($this->with_menu)
        {
            $width = 80;
        }
        else
        {
            $width = 100;
        }
        echo '<div style="float: right; width: ' . $width . '%;">';
    }
    
    function display_footer()
    {
        echo '<div class="clear"></div>';
        echo '</div>';
        
        parent :: display_footer();
    }

    function get_trackers()
    {
        return $this->trackers;
    }

    private function set_publication_variables($survey_publication_id)
    {
        $this->pid = $survey_publication_id;
        $this->pub = $this->datamanager->retrieve_survey_publication($this->pid);
        $survey_id = $this->pub->get_content_object();
        $this->survey = RepositoryDataManager :: get_instance()->retrieve_content_object($survey_id);
        $this->set_parameter(SurveyManager :: PARAM_SURVEY_PUBLICATION, $this->pid);
    }

    function get_menu_html()
    {
        $survey_menu = new SurveyMenu($this->get_participant());
        $html = array();
        $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
        $html[] = $survey_menu->render_as_tree();
        $html[] = '</div>';
        return implode("\n", $html);
    }

    function get_survey_html()
    {
        $display = ComplexDisplay :: factory($this, $this->survey->get_type());
        $display->run();
    }
    
    function get_template_id()
    {
    	return $this->active_tracker->get_context_template_id();
    }
    
    function get_participant_id()
    {
    	return $this->active_tracker->get_id();	
    }
    
	function get_root_content_object()
    {
    	return $this->survey;
    }

    function get_participant()
    {
        return $this->get_parameter(SurveyManager :: PARAM_SURVEY_PARTICIPANT);
    }

    function save_answer($complex_question_id, $answer)
    {

        $dummy = new SurveyQuestionAnswerTracker();
        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID, $this->active_tracker->get_id());
        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_QUESTION_CID, $complex_question_id);
        $condition = new AndCondition($conditions);
        $trackers = $dummy->retrieve_tracker_items($condition);
        if (count($trackers) === 1)
        {
            $trackers[0]->set_answer($answer);
            $trackers[0]->update();
        }
        else
        {
            $parameters = array();
            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID] = $this->active_tracker->get_id();
            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_ID] = $this->active_tracker->get_context_id();
            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_QUESTION_CID] = $complex_question_id;
            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_ANSWER] = $answer;

            Events :: trigger_event('attempt_question', 'survey', $parameters);
        }
        //test for better tracing of setting status of trackers.
        
        
        
    }

    function finish_survey($percent)
    {
        $tracker = $this->active_tracker;
        $tracker->set_progress($percent);
        if($percent >= 100){
        	 $tracker->set_status(SurveyParticipantTracker :: STATUS_FINISHED);
        }
        $tracker->set_total_time($tracker->get_total_time() + (time() - $tracker->get_start_time()));
        $tracker->update();

//        $track = new SurveyParticipantTracker();
//        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $this->pid);
//        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $this->active_tracker->get_user_id());
//        $condition = new AndCondition($conditions);
//        $trackers = $track->retrieve_tracker_items($condition);
//
//        if ($percent >= 100)
//        {
//            $all_finished = false;
//            $progress = array();
//
//            foreach ($trackers as $tracker)
//            {
//                $progress[] = $tracker->get_progress();
//            }
//
//            $finshed = array_intersect($progress, array(100));
//            $all_finished = count($progress) == count($finshed);
//            if ($all_finished)
//            {
//                foreach ($trackers as $tracker)
//                {
//                    $tracker->set_status(SurveyParticipantTracker :: STATUS_FINISHED);
//                    $tracker->update();
//                }
//            }
//
//        }
//
        foreach ($trackers as $tracker)
        {
            $status = $tracker->get_status();
            if ($status === SurveyParticipantTracker :: STATUS_NOTSTARTED)
            {
                $tracker->set_status(SurveyParticipantTracker :: STATUS_STARTED);
                $tracker->update();
            }

        }

    }

    function get_current_attempt_id()
    {
        return $this->active_tracker->get_id();
    }

    function get_go_back_url()
    {
        return $this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS, SurveyManager :: PARAM_SURVEY_PUBLICATION => null, SurveyManager :: PARAM_SURVEY_PARTICIPANT => null));
    }

    function parse($value)
    {

		$context = $this->survey->get_context_instance();
        $explode = explode('$V{', $value);
		      
        
        $new_value = array();
        foreach ($explode as $part)
        {

            $vars = explode('}', $part);

            if (count($vars) == 1)
            {
                $new_value[] = $vars[0];
            }
            else
            {
                $var = $vars[0];

                $replace = $context->get_additional_property($var);

                $new_value[] = $replace . ' ' . $vars[1];
            }

        }
        return implode(' ', $new_value);
    }
    
//    function get_total_questions(){
//    	return $this->survey->count_pages();
//    }
//    
//	function get_total_pages(){
//    	return $this->survey->count_pages();
//    }
    
	function get_survey(){
    	return $this->survey;
    }
    
}

?>
	
	private $datamanager;
    
    private $pub;
    private $invitation;
    private $assessment;
    private $iid;
    private $pid;
    private $active_tracker;
    private $trail;

    function run()
    {
        // Retrieving assessment
        $this->datamanager = WeblcmsDataManager :: get_instance();
        if (Request :: get(Tool :: PARAM_PUBLICATION_ID))
        {
            $this->pid = Request :: get(Tool :: PARAM_PUBLICATION_ID);
            $this->pub = $this->datamanager->retrieve_content_object_publication($this->pid);
            $this->assessment = $this->pub->get_content_object();
            $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, $this->pid);
        }
        
        if (Request :: get(AssessmentTool :: PARAM_INVITATION_ID))
        {
            $this->iid = Request :: get(AssessmentTool :: PARAM_INVITATION_ID);
            $condition = new EqualityCondition(SurveyInvitation :: PROPERTY_INVITATION_CODE, $this->iid);
            $this->invitation = $this->datamanager->retrieve_survey_invitations($condition)->next_result();
            $this->pub = $this->datamanager->retrieve_content_object_publication($this->invitation->get_survey_id());
            $this->pid = $this->pub->get_id();
            $this->assessment = $this->pub->get_content_object();
            $this->set_parameter(AssessmentTool :: PARAM_INVITATION_ID, $this->iid);
        }
        
        // Checking statistics
        

        $track = new WeblcmsAssessmentAttemptsTracker();
        $conditions[] = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $this->pid);
        $conditions[] = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_USER_ID, $this->get_user_id());
        $condition = new AndCondition($conditions);
        $trackers = $track->retrieve_tracker_items($condition);
        
        $count = count($trackers);
        
        foreach ($trackers as $tracker)
        {
            if ($tracker->get_status() == 'not attempted')
            {
                $this->active_tracker = $tracker;
                $count --;
                break;
            }
        }
        
        if ($this->assessment->get_maximum_attempts() != 0 && $count >= $this->assessment->get_maximum_attempts())
        {
            Display :: not_allowed();
            return;
        }
        
        if (! $this->active_tracker)
        {
            $this->active_tracker = $this->create_tracker();
        }
        
        // Executing assessment
        

        if ($this->assessment->get_assessment_type() == Hotpotatoes :: TYPE_HOTPOTATOES)
        {
            $this->display_header(BreadcrumbTrail :: get_instance());
            
            $path = $this->assessment->add_javascript(Path :: get(WEB_PATH) . 'application/lib/weblcms/ajax/hotpotatoes_save_score.php', $this->get_go_back_url(), $this->active_tracker->get_id());
            //$path = $this->assessment->get_test_path();
            echo '<iframe src="' . $path . '" width="100%" height="600">
  				 <p>Your browser does not support iframes.</p>
				 </iframe>';
            //require_once $path;
            $this->display_footer();
            exit();
        }
        else
        {
            $this->trail = BreadcrumbTrail :: get_instance();
            $this->trail->add(new Breadcrumb($this->get_url(array()), Translation :: get('TakeAssessment')));
        	$display = ComplexDisplay :: factory($this, $this->assessment->get_type());
            
            //$this->display_header(new BreadcrumbTrail());
            $display->run();
            //$this->display_footer();
        }
    
    }
    
	function get_root_content_object()
    {
    	return $this->assessment;
    }
    
	function display_header($trail)
    {    	
    	return parent :: display_header($this->trail);
    }

    function create_tracker()
    {
        $args = array('assessment_id' => $this->pid, 'user_id' => $this->get_user_id(), 'course_id' => $this->get_course_id(), 'total_score' => 0);
        
        $tracker = Events :: trigger_event('attempt_assessment', 'weblcms', $args);
        
        return $tracker[0];
    }


    function save_answer($complex_question_id, $answer, $score)
    {
        $parameters = array();
        $parameters['assessment_attempt_id'] = $this->active_tracker->get_id();
        $parameters['question_cid'] = $complex_question_id;
        $parameters['answer'] = $answer;
        $parameters['score'] = $score;
        $parameters['feedback'] = '';
        
        Events :: trigger_event('attempt_question', 'weblcms', $parameters);
    }

    function finish_assessment($total_score)
    {
        $tracker = $this->active_tracker;
        
        $tracker->set_total_score($total_score);
        $tracker->set_total_time($tracker->get_total_time() + (time() - $tracker->get_start_time()));
        $tracker->set_status('completed');
        $tracker->update();
    }

    function get_current_attempt_id()
    {
        return $this->active_tracker->get_id();
    }

    function get_go_back_url()
    {
        return $this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW));
    }
}
?>