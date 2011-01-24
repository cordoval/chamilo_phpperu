<?php
namespace application\survey;

use common\libraries\Security;
use common\libraries\Utilities;
use common\libraries\AjaxManager;
use common\libraries\JsonAjaxResult;
use common\libraries\Request;
use common\libraries\Path;
use repository\content_object\survey_page\SurveyPage;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use tracking\Tracker;
use tracking\Event;


use repository\RepositoryDataManager;

class SurveyAjaxDeleteAnswer extends AjaxManager
{
    
    const PARAM_SURVEY_PUBLICATION_ID = 'survey_publication';
    const PARAM_CONTEXT_PATH = 'context_path';
    const PARAM_SUCCES = 'succes';

    private $publication_id;
    
    /* (non-PHPdoc)
     * @see common\libraries.AjaxManager::required_parameters()
     */
    function required_parameters()
    {
        return array(self :: PARAM_SURVEY_PUBLICATION_ID, self :: PARAM_CONTEXT_PATH);
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
     	        
        $this->set_participant_tracker();
        
        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID, $this->participant_tracker->get_id());
        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID, $complex_question_id);
        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_PATH, $context_path);
        $condition = new AndCondition($conditions);
        $tracker = $trackers = Tracker :: get_data(SurveyQuestionAnswerTracker :: CLASS_NAME, SurveyManager :: APPLICATION_NAME, $condition, 0, 1)->next_result();
        
        if ($tracker)
        {
           $succes = $tracker->delete();
        }else{
        	$succes = false;
        }
                
        $result = new JsonAjaxResult(200);
        $result->set_property(self :: PARAM_SUCCES, $succes);
        $result->display();
    
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