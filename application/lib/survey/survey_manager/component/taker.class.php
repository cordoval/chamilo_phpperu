<?php

require_once Path :: get_application_path() . 'lib/survey/trackers/survey_participant_tracker.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/survey/display/component/viewer/survey_viewer_wizard.class.php';


class SurveyManagerTakerComponent extends SurveyManager
{
    private $survey_id;
    private $publication_id;
    private $invitee_id;
    
    private $participant_tracker;

    function run()
    {
        
        $this->survey_id = Request :: get(SurveyViewerWizard :: PARAM_SURVEY_ID);
        
        $this->publication_id = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
         
        $this->invitee_id = Request :: get(SurveyViewerWizard :: PARAM_INVITEE_ID);
               
        if (! SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_PARTICIPATE, $this->publication_id, SurveyRights :: TYPE_PUBLICATION, $this->user_id))
        {
            Display :: not_allowed();
        }
        
        ComplexDisplay :: launch(Survey :: get_type_name(), $this, false);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE)), Translation :: get('BrowseSurveys')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PUBLICATION_ID, SurveyViewerWizard :: PARAM_SURVEY_ID, SurveyViewerWizard :: PARAM_INVITEE_ID, SurveyViewerWizard :: PARAM_CONTEXT_PATH);
    }

    //try out for interface SurveyTaker


    function started()
    {
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $this->publication_id);
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $this->invitee_id);
        $condition = new AndCondition($conditions);
        
        $tracker_count = Tracker :: count_data(SurveyParticipantTracker :: get_table_name(), SurveyManager :: APPLICATION_NAME, $condition);
        
        if ($tracker_count == 0)
        {
            
            $args = array();
            $args[SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID] = $this->publication_id;
            $args[SurveyParticipantTracker :: PROPERTY_USER_ID] = $this->invitee_id;
            $args[SurveyParticipantTracker :: PROPERTY_START_TIME] = time();
            $args[SurveyParticipantTracker :: PROPERTY_STATUS] = SurveyParticipantTracker :: STATUS_STARTED;
            $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] = 0;
            $args[SurveyParticipantTracker :: PROPERTY_PARENT_ID] = 0;
            $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_ID] = 0;
            $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_NAME] = 'NOCONTEXT';
            $this->participant_tracker = Event :: trigger(SurveyParticipantTracker :: CREATE_PARTICIPANT_EVENT, SurveyManager :: APPLICATION_NAME, $args);
        }else{
        	$this->participant_tracker = Tracker :: get_data(SurveyParticipantTracker :: get_table_name(), SurveyManager :: APPLICATION_NAME, $condition, 0, 1)->next_result();
        }
    }

    function finish()
    {
    
    }

    function save_answer($complex_question_id, $answer, $context_path)
    {
        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID, $this->participant_tracker->get_id());
        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID, $complex_question_id);
        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_PATH, $context_path);
        $condition = new AndCondition($conditions);
        $tracker = $trackers = tracker :: get_data(SurveyQuestionAnswerTracker :: get_table_name(), SurveyManager :: APPLICATION_NAME, $condition, 0, 1)->next_result();
        
        if ($tracker)
        {
            $tracker->set_answer($answer);
            $tracker->update();
        }
        else
        {
            $parameters = array();
            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID] = $this->participant_tracker->get_id();
            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID] = $complex_question_id;
            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_ANSWER] = $answer;
            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_PATH] = $context_path;
            
            Event :: trigger(SurveyQuestionAnswerTracker :: SAVE_QUESTION_ANSWER_EVENT, SurveyManager :: APPLICATION_NAME, $parameters);
        }
    }
	
    function get_answer($complex_question_id, $context_path){
    	
    	$conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID, $this->participant_tracker->get_id());
        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID, $complex_question_id);
        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_PATH, $context_path);
        $condition = new AndCondition($conditions);
        $tracker = $trackers = tracker :: get_data(SurveyQuestionAnswerTracker :: get_table_name(), SurveyManager :: APPLICATION_NAME, $condition, 0, 1)->next_result();
              
        if ($tracker)
        {
        	return $tracker->get_answer();
        }
        else
        {
        	return null;
        }
    }
}

?>