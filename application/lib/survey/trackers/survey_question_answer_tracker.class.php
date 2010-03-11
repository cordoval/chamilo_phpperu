<?php
/**
 * $Id: survey_question_attempts_tracker.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.trackers
 */

class SurveyQuestionAnswerTracker extends MainTracker
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_SURVEY_PARTICIPANT_ID = 'survey_participant_id';
    const PROPERTY_CONTEXT_ID = 'context_id';
    const PROPERTY_QUESTION_CID = 'question_cid';
    const PROPERTY_ANSWER = 'answer';

    /**
     * Constructor sets the default values
     */
    function SurveyQuestionAnswerTracker()
    {
        parent :: MainTracker('survey_question_answer_tracker');
    }

    /**
     * Inherited
     * @see MainTracker :: track()
     */
    function track($parameters = array())
    {
        $survey_participant_id = $parameters[self :: PROPERTY_SURVEY_PARTICIPANT_ID];
        $context_id = $parameters[self :: PROPERTY_CONTEXT_ID];
        $question_cid = $parameters[self :: PROPERTY_QUESTION_CID];
        $answer = $parameters[self :: PROPERTY_ANSWER];
        
        $this->set_survey_participant_id($survey_participant_id);
        $this->set_context_id($context_id);
        $this->set_question_cid($question_cid);
        $this->set_answer($answer);
        
        $this->create();
        
        return $this;
    }

    /**
     * Inherited
     * @see MainTracker :: is_summary_tracker
     */
    function is_summary_tracker()
    {
        return false;
    }

    /**
     * Inherited
     */
    function get_default_property_names()
    {
        return array_merge(parent :: get_default_property_names(), array(self :: PROPERTY_SURVEY_PARTICIPANT_ID, self :: PROPERTY_QUESTION_CID, self :: PROPERTY_ANSWER));
    }

    function get_survey_participant_id()
    {
        return $this->get_property(self :: PROPERTY_SURVEY_PARTICIPANT_ID);
    }

    function set_survey_participant_id($survey_participant_id)
    {
        $this->set_property(self :: PROPERTY_SURVEY_PARTICIPANT_ID, $survey_participant_id);
    }

    function get_context_id()
    {
        return $this->get_property(self :: PROPERTY_CONTEXT_ID);
    }

    function set_context_id($context_id)
    {
        $this->set_property(self :: PROPERTY_CONTEXT_ID, $context_id);
    }

    function get_question_cid()
    {
        return $this->get_property(self :: PROPERTY_QUESTION_CID);
    }

    function set_question_cid($question_cid)
    {
        $this->set_property(self :: PROPERTY_QUESTION_CID, $question_cid);
    }

    function get_answer()
    {
        return $this->get_property(self :: PROPERTY_ANSWER);
    }

    function set_answer($answer)
    {
        $this->set_property(self :: PROPERTY_ANSWER, $answer);
    }

    function empty_tracker($event)
    {
        $this->remove();
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>