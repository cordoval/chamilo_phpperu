<?php
/**
 * $Id: phrases_question_attempts_tracker.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.trackers
 */

class PhrasesQuestionAttemptsTracker extends MainTracker
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_ASSESSMENT_ATTEMPT_ID = 'assessment_attempt_id';
    const PROPERTY_COMPLEX_QUESTION_ID = 'complex_question_id';
    const PROPERTY_DATE = 'date';
    const PROPERTY_ANSWER = 'answer';
    const PROPERTY_FEEDBACK = 'feedback';
    const PROPERTY_SCORE = 'score';

    /**
     * Constructor sets the default values
     */
    function PhrasesQuestionAttemptsTracker()
    {
        parent :: MainTracker('phrases_question_attempts_tracker');
    }

    /**
     * Inherited
     * @see MainTracker :: track()
     */
    function track($parameters = array())
    {
        $assessment_attempt_id = $parameters['assessment_attempt_id'];
        $complex_question_id = $parameters['complex_question_id'];
        $date = $parameters['date'];
        $answer = $parameters['answer'];
        $feedback = $parameters['feedback'];
        $score = $parameters['score'];

        $this->set_assessment_attempt_id($assessment_attempt_id);
        $this->set_complex_question_id($complex_question_id);
        $this->set_date($date);
        $this->set_answer($answer);
        $this->set_feedback($feedback);
        $this->set_score($score);

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
        return array_merge(parent :: get_default_property_names(), array(self :: PROPERTY_ASSESSMENT_ATTEMPT_ID, self :: PROPERTY_QUESTION_ID, self :: PROPERTY_DATE, self :: PROPERTY_ANSWER, self :: PROPERTY_FEEDBACK, self :: PROPERTY_SCORE));
    }

    function get_assessment_attempt_id()
    {
        return $this->get_property(self :: PROPERTY_ASSESSMENT_ATTEMPT_ID);
    }

    function set_assessment_attempt_id($assessment_attempt_id)
    {
        $this->set_property(self :: PROPERTY_ASSESSMENT_ATTEMPT_ID, $assessment_attempt_id);
    }

    function get_complex_question_id()
    {
        return $this->get_property(self :: PROPERTY_COMPLEX_QUESTION_ID);
    }

    function set_complex_question_id($complex_question_id)
    {
        $this->set_property(self :: PROPERTY_COMPLEX_QUESTION_ID, $complex_question_id);
    }

    function get_date()
    {
        return $this->get_property(self :: PROPERTY_DATE);
    }

    function set_date($date)
    {
        $this->set_property(self :: PROPERTY_DATE, $date);
    }

    function get_answer()
    {
        return $this->get_property(self :: PROPERTY_ANSWER);
    }

    function set_answer($answer)
    {
        $this->set_property(self :: PROPERTY_ANSWER, $answer);
    }

    function get_score()
    {
        return $this->get_property(self :: PROPERTY_SCORE);
    }

    function set_score($score)
    {
        $this->set_property(self :: PROPERTY_SCORE, $score);
    }

    function get_feedback()
    {
        return $this->get_property(self :: PROPERTY_FEEDBACK);
    }

    function set_feedback($feedback)
    {
        $this->set_property(self :: PROPERTY_FEEDBACK, $feedback);
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