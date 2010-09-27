<?php

require_once Path :: get_application_path() . 'lib/survey/trackers/survey_participant_tracker.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/survey/display/component/viewer/survey_viewer_wizard.class.php';


class SurveyManagerTakerComponent extends SurveyManager
{
    private $survey_id;
    private $invitee_id;

    function run()
    {
        
        $this->survey_id = Request :: get(SurveyManager :: PARAM_SURVEY_ID);
        
//        $this->set_parameter(SurveyManager :: PARAM_SURVEY_ID, $this->survey_id);
        
//        
//        $this->publication_id = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
//        
//        $this->set_parameter(SurveyManager :: PARAM_PUBLICATION_ID, $this->publication_id);
        
//        $this->set_parameter(SurveyManager :: PARAM_PARTICIPANT_ID, Request :: get(SurveyManager :: PARAM_PARTICIPANT_ID));
        
        $this->invitee_id = Request :: get(SurveyManager :: PARAM_INVITEE_ID);
        
//        dump($this->invitee_id);
        
//        if (!$this->invitee_id)
//        {
//            $this->invitee_id = $this->get_user_id();
//            $this->set_parameter(SurveyManager :: PARAM_INVITEE_ID, $this->invitee_id);
//        }
               
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
        return array(SurveyViewerWizard :: PARAM_SURVEY_ID, SurveyViewerWizard :: PARAM_INVITEE_ID, SurveyViewerWizard :: PARAM_CONTEXT_ID, SurveyViewerWizard :: PARAM_CONTEXT_PATH, SurveyViewerWizard :: PARAM_CONTEXT_TEMPLATE_ID, SurveyViewerWizard :: PARAM_TEMPLATE_ID);
    }

    //try out for interface SurveyTaker
    

    function get_invitee_id()
    {
        return $this->invitee_id;
    }

    function started($survey_id)
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
            $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] = 0;
            $args[SurveyParticipantTracker :: PROPERTY_PARENT_ID] = 0;
            $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_ID] = 0;
            $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_NAME] = 'NOCONTEXT';
            $tracker = Event :: trigger(SurveyParticipantTracker :: CREATE_PARTICIPANT_EVENT, SurveyManager :: APPLICATION_NAME, $args);
            $succes = true;
        
        }
    }

    function finish($survey_id)
    {
    
    }

    function started_context($survey_id, $context_template, $context_id)
    {
        
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $this->publication_id);
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $this->invitee_id);
        $condition = new AndCondition($conditions);
        
        $tracker_count = Tracker :: count_data(SurveyParticipantTracker :: get_table_name(), SurveyManager :: APPLICATION_NAME, $condition);
        
        if ($tracker_count == 0)
        {
            
            $args = array();
            $args[SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID] = $this->publication_id;
            $args[SurveyParticipantTracker :: PROPERTY_USER_ID] =  $this->invitee_id;
            
//            $context_template = $survey->get_context_template();
            
            
            $tracker_matrix = array();
            $level_matrix[] = $context_template->get_id();
            $context_template_children = $context_template->get_children(true);
            while ($child_template = $context_template_children->next_result())
            {
                $level_matrix[] = $child_template->get_id();
            }
            $tracker_matrix = array();
            
            $condition = new EqualityCondition(SurveyTemplate :: PROPERTY_USER_ID, $this->invitee_id, SurveyTemplate :: get_table_name());
            $templates = SurveyContextDataManager :: get_instance()->retrieve_survey_templates($context_template->get_type(), $condition);
            
            while ($template = $templates->next_result())
            {
                $property_names = $template->get_additional_property_names(true);
                $level = 0;
                $parent_level_context_id = 0;
                
                foreach ($property_names as $property_name => $context_type)
                {
                    $context_template_id = $level_matrix[$level];
                    
                    if ($tracker_matrix[$level - 1][$parent_level_context_id])
                    {
                        $parent_id = $tracker_matrix[$level - 1][$parent_level_context_id];
                    }
                    else
                    {
                        $parent_id = 0;
                    }
                    
                    $args[SurveyParticipantTracker :: PROPERTY_PARENT_ID] = $parent_id;
                    $context_id = $template->get_additional_property($property_name);
                    $parent_level_context_id = $context_id;
                    
                    if ($tracker_matrix[$level][$context_id])
                    {
                        $level ++;
                        continue;
                    }
                    else
                    {
                        
                        $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] = $context_template_id;
                        $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_ID] = $context_id;
                        $context = SurveyContextDataManager :: get_instance()->retrieve_survey_context_by_id($context_id);
                        
                        $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_NAME] = $context->get_name();
                        $tracker = Event :: trigger(SurveyParticipantTracker :: CREATE_PARTICIPANT_EVENT, SurveyManager :: APPLICATION_NAME, $args);
                        $tracker_matrix[$level][$context_id] = $tracker[0]->get_id();
                        $succes = true;
                    }
                    
                    $level ++;
                }
            }
        }
       
    }

    function finish_context($survey, $template_id, $context_id)
    {
    
    }

    function save_answers($question_id, $answer, $template_id, $context_id)
    {
//        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID, $this->tracker->get_id());
//        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_QUESTION_CID, $complex_question_id);
//        $condition = new AndCondition($conditions);
//        $tracker_count = $trackers = tracker :: count_data(SurveyQuestionAnswerTracker :: get_table_name(), SurveyManager :: APPLICATION_NAME, $condition);
//        
//        if ($tracker_count == 1)
//        {
//            $tracker = tracker :: get_data(SurveyQuestionAnswerTracker :: get_table_name(), SurveyManager :: APPLICATION_NAME, $condition, 0, 1)->next_result();
//            $tracker->set_answer($answer);
//            $tracker->update();
//        }
//        else
//        {
//            $parameters = array();
//            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID] = $this->tracker->get_id();
//            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_ID] = $this->tracker->get_context_id();
//            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_QUESTION_CID] = $complex_question_id;
//            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_ANSWER] = $answer;
//            
//            Event :: trigger(SurveyQuestionAnswerTracker :: SAVE_QUESTION_ANSWER_EVENT, SurveyManager :: APPLICATION_NAME, $parameters);
//        }
    }

}

?>