<?php
namespace application\weblcms;

use common\libraries\Utilities;
use tracking\SimpleTracker;

/**
 * $Id: survey_question_attempts_tracker.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.trackers
 */

class WeblcmsSurveyQuestionAnswerTracker extends SimpleTracker
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_SURVEY_PARTICIPANT_ID = 'survey_participant_id';
    const PROPERTY_CONTEXT_ID = 'context_id';
    const PROPERTY_QUESTION_CID = 'question_cid';
    const PROPERTY_ANSWER = 'answer';

    function validate_parameters(array $parameters = array())
    {
        $this->set_survey_participant_id($parameters[self :: PROPERTY_SURVEY_PARTICIPANT_ID]);
        $this->set_context_id($parameters[self :: PROPERTY_CONTEXT_ID]);
        $this->set_question_cid($parameters[self :: PROPERTY_QUESTION_CID]);
        $this->set_answer($parameters[self :: PROPERTY_ANSWER]);
    }

    /**
     * Inherited
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_SURVEY_PARTICIPANT_ID, self :: PROPERTY_QUESTION_CID, self :: PROPERTY_ANSWER));
    }

    function get_survey_participant_id()
    {
        return $this->get_default_property(self :: PROPERTY_SURVEY_PARTICIPANT_ID);
    }

    function set_survey_participant_id($survey_participant_id)
    {
        $this->set_default_property(self :: PROPERTY_SURVEY_PARTICIPANT_ID, $survey_participant_id);
    }

    function get_context_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTEXT_ID);
    }

    function set_context_id($context_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTEXT_ID, $context_id);
    }

    function get_question_cid()
    {
        return $this->get_default_property(self :: PROPERTY_QUESTION_CID);
    }

    function set_question_cid($question_cid)
    {
        $this->set_default_property(self :: PROPERTY_QUESTION_CID, $question_cid);
    }

    function get_answer()
    {

        if ($result = unserialize($this->get_default_property(self :: PROPERTY_ANSWER)))
        {
            return $result;
        }
        return array();
    }

    function set_answer($answer)
    {
        $this->set_default_property(self :: PROPERTY_ANSWER, $answer);
    }

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}
?>