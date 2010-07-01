<?php
/**
 * @package application.lib.weblcms.trackers
 */
class WeblcmsLearningPathQuestionAttemptsTracker extends SimpleTracker
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_LPI_ATTEMPT_ID = 'lpi_attempt_id';
    const PROPERTY_QUESTION_CID = 'question_cid';
    const PROPERTY_ANSWER = 'answer';
    const PROPERTY_FEEDBACK = 'feedback';
    const PROPERTY_SCORE = 'score';

    function validate_parameters(array $parameters = array())
    {
        $this->set_lpi_attempt_id($parameters[self :: PROPERTY_LPI_ATTEMPT_ID]);
        $this->set_question_cid($parameters[self :: PROPERTY_QUESTION_CID]);
        $this->set_answer($parameters[self :: PROPERTY_ANSWER]);
        $this->set_feedback($parameters[self :: PROPERTY_FEEDBACK]);
        $this->set_score($parameters[self :: PROPERTY_SCORE]);
    }

    static function get_default_default_property_names()
    {
        return parent :: get_default_default_property_names(array(self :: PROPERTY_LPI_ATTEMPT_ID, self :: PROPERTY_QUESTION_CID, self :: PROPERTY_ANSWER, self :: PROPERTY_FEEDBACK, self :: PROPERTY_SCORE));
    }

    function get_lpi_attempt_id()
    {
        return $this->get_default_property(self :: PROPERTY_LPI_ATTEMPT_ID);
    }

    function set_lpi_attempt_id($lpi_attempt_id)
    {
        $this->set_default_property(self :: PROPERTY_LPI_ATTEMPT_ID, $lpi_attempt_id);
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
        return $this->get_default_property(self :: PROPERTY_ANSWER);
    }

    function set_answer($answer)
    {
        $this->set_default_property(self :: PROPERTY_ANSWER, $answer);
    }

    function get_score()
    {
        return $this->get_default_property(self :: PROPERTY_SCORE);
    }

    function set_score($score)
    {
        $this->set_default_property(self :: PROPERTY_SCORE, $score);
    }

    function get_feedback()
    {
        return $this->get_default_property(self :: PROPERTY_FEEDBACK);
    }

    function set_feedback($feedback)
    {
        $this->set_default_property(self :: PROPERTY_FEEDBACK, $feedback);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>