<?php
namespace application\survey;

use common\libraries\Path;

class SurveyQuestionAnswerTracker extends SimpleTracker
{
    const CLASS_NAME = __CLASS__;

    const SAVE_QUESTION_ANSWER_EVENT = 'save_question_answer';

    const PROPERTY_SURVEY_PARTICIPANT_ID = 'survey_participant_id';
    const PROPERTY_CONTEXT_ID = 'context_id';
    const PROPERTY_COMPLEX_QUESTION_ID = 'question_cid';
    const PROPERTY_ANSWER = 'answer';
    const PROPERTY_CONTEXT_PATH = 'context_path';
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_CONTEXT_TEMPLATE_ID = 'context_template_id';

    function validate_parameters(array $parameters = array())
    {
        $this->set_survey_participant_id($parameters[self :: PROPERTY_SURVEY_PARTICIPANT_ID]);
        $this->set_context_id($parameters[self :: PROPERTY_CONTEXT_ID]);
        $this->set_question_cid($parameters[self :: PROPERTY_COMPLEX_QUESTION_ID]);
        $this->set_answer($parameters[self :: PROPERTY_ANSWER]);
        $this->set_context_path($parameters[self :: PROPERTY_CONTEXT_PATH]);
        $this->set_publication_id($parameters[self :: PROPERTY_PUBLICATION_ID]);
        $this->set_user_id($parameters[self :: PROPERTY_USER_ID]);
        $this->set_context_template_id($parameters[self :: PROPERTY_CONTEXT_TEMPLATE_ID]);
    }

    /**
     * Inherited
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_SURVEY_PARTICIPANT_ID, self :: PROPERTY_COMPLEX_QUESTION_ID, self :: PROPERTY_ANSWER, self :: PROPERTY_CONTEXT_PATH, self :: PROPERTY_PUBLICATION_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_CONTEXT_TEMPLATE_ID));
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

    function get_context_path()
    {
        return $this->get_default_property(self :: PROPERTY_CONTEXT_ID);
    }

    function set_context_path($context_path)
    {
        $this->set_default_property(self :: PROPERTY_CONTEXT_PATH, $context_path);
    }

    function get_question_cid()
    {
        return $this->get_default_property(self :: PROPERTY_COMPLEX_QUESTION_ID);
    }

    function set_question_cid($question_cid)
    {
        $this->set_default_property(self :: PROPERTY_COMPLEX_QUESTION_ID, $question_cid);
    }

    function get_answer()
    {
        $answer = unserialize($this->get_default_property(self :: PROPERTY_ANSWER));

        if ($answer)
        {
            return $answer;
        }
        else
        {
            return array();
        }

    }

    function set_answer($answer)
    {
        $this->set_default_property(self :: PROPERTY_ANSWER, $answer);
    }

    function get_publication_id()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION_ID);
    }

    function set_publication_id($publication_id)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION_ID, $publication_id);
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_context_template_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTEXT_TEMPLATE_ID);
    }

    function set_context_template_id($context_template_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTEXT_TEMPLATE_ID, $context_template_id);
    }

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}
?>