<?php
namespace application\survey;

use common\libraries\Security;
use common\libraries\Utilities;
use common\libraries\AjaxManager;
use common\libraries\JsonAjaxResult;
use common\libraries\Request;
use common\libraries\StringUtilities;
use common\libraries\Path;
use repository\content_object\survey_page\SurveyPage;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use tracking\Tracker;
use tracking\Event;

use repository\RepositoryDataManager;

class SurveyAjaxSaveAnswer extends AjaxManager
{
    
    const PARAM_SURVEY_PUBLICATION_ID = 'survey_publication';
    const PARAM_ANSWER = 'answer';
    const PARAM_CONTEXT_PATH = 'context_path';
    const PARAM_SUCCES = 'succes';
    
    private $publication_id;

    /* (non-PHPdoc)
     * @see common\libraries.AjaxManager::required_parameters()
     */
    function required_parameters()
    {
        return array(self :: PARAM_SURVEY_PUBLICATION_ID, self :: PARAM_ANSWER, self :: PARAM_CONTEXT_PATH);
    }

    /* (non-PHPdoc)
     * @see common\libraries.AjaxManager::run()
     */
    function run()
    {
        
        $this->publication_id = $this->get_parameter(self :: PARAM_SURVEY_PUBLICATION_ID);
        
        $context_path = $this->get_parameter(self :: PARAM_CONTEXT_PATH);
        $ids = explode('_', $context_path);
        $complex_question_id = array_pop($ids);
        $answers = $this->get_parameter(self :: PARAM_ANSWER);
        $answers = json_decode($answers, true);
        
//        dump($answers);
        
        foreach ($answers as $key => $value)
        {
            $value = Security :: remove_XSS($value);
            $split_key = explode('_', $key);
//            dump( $split_key);
            $count = count($split_key);
//            dump($count);
            if (!StringUtilities :: is_null_or_empty($value, true))
            {
                $answer_index = $split_key[1];
//              dump($answer_index);
                if ($count == 3)
                {
                    $sub_index = $split_key[2];
                    $answer[$answer_index][$sub_index] = $value;
                }
                else
                {
//                    $answer[$answer_index] = $value;
                     $answer[$key] = $value;
                }
            }
        
        }
        
//        dump($answer);
        
        if ($answer)
        {
            
            $this->set_participant_tracker();
            
            $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID, $this->participant_tracker->get_id());
            $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID, $complex_question_id);
            $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_PATH, $context_path);
            $condition = new AndCondition($conditions);
            $tracker = $trackers = Tracker :: get_data(SurveyQuestionAnswerTracker :: CLASS_NAME, SurveyManager :: APPLICATION_NAME, $condition, 0, 1)->next_result();
            
            if ($tracker)
            {
                $tracker->set_answer($answer);
                $succes = $tracker->update();
            }
            else
            {
                $parameters = array();
                $parameters[SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID] = $this->participant_tracker->get_id();
                $parameters[SurveyQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID] = $complex_question_id;
                $parameters[SurveyQuestionAnswerTracker :: PROPERTY_ANSWER] = $answer;
                $parameters[SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_PATH] = $context_path;
                $parameters[SurveyQuestionAnswerTracker :: PROPERTY_PUBLICATION_ID] = $this->publication_id;
                $parameters[SurveyQuestionAnswerTracker :: PROPERTY_USER_ID] = $this->get_user_id();
                
                $publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($this->publication_id);
                $survey = $publication->get_publication_object();
                
                if ($survey->has_context())
                {
                    $level_count = $survey->count_levels();
                    $path_ids = explode('|', $context_path);
                    $context_ids = explode('_', $path_ids[1]);
                    $context_count = count($context_ids);
                    $context_id = array_pop($context_ids);
                    $context_template = $survey->get_context_template_for_level($context_count);
                    $parameters[SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] = $context_template->get_id();
                    $parameters[SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_ID] = $context_id;
                
                }
                else
                {
                    $parameters[SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_ID] = 0;
                    $parameters[SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] = 0;
                }
                $succes = Event :: trigger(SurveyQuestionAnswerTracker :: SAVE_QUESTION_ANSWER_EVENT, SurveyManager :: APPLICATION_NAME, $parameters);
            }
            
            $result = new JsonAjaxResult(200);
            $result->set_property(self :: PARAM_SUCCES, $succes);
            $result->display();
        }
        else
        {
            $result = new JsonAjaxResult(200);
            $result->set_property(self :: PARAM_SUCCES, false);
            $result->display();
        }
    
    }

    function set_participant_tracker()
    {
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $this->publication_id);
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $this->get_user_id());
        $condition = new AndCondition($conditions);
        
        $tracker_count = Tracker :: count_data(SurveyParticipantTracker :: CLASS_NAME, SurveyManager :: APPLICATION_NAME, $condition);
        
        if ($tracker_count == 0)
        {
            
            $args = array();
            $args[SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID] = $this->publication_id;
            $args[SurveyParticipantTracker :: PROPERTY_USER_ID] = $this->get_user_id();
            $args[SurveyParticipantTracker :: PROPERTY_START_TIME] = time();
            $args[SurveyParticipantTracker :: PROPERTY_STATUS] = SurveyParticipantTracker :: STATUS_STARTED;
            $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] = 0;
            $args[SurveyParticipantTracker :: PROPERTY_PARENT_ID] = 0;
            $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_ID] = 0;
            $args[SurveyParticipantTracker :: PROPERTY_CONTEXT_NAME] = 'NOCONTEXT';
            $trackers = Event :: trigger(SurveyParticipantTracker :: CREATE_PARTICIPANT_EVENT, SurveyManager :: APPLICATION_NAME, $args);
            $this->participant_tracker = $trackers[0];
        }
        else
        {
            $this->participant_tracker = Tracker :: get_data(SurveyParticipantTracker :: CLASS_NAME, SurveyManager :: APPLICATION_NAME, $condition, 0, 1)->next_result();
        }
    }

}
?>