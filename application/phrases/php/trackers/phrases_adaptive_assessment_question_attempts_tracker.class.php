<?php
namespace application\phrases;
use common\libraries\Utilities;
use tracking\SimpleTracker;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

class PhrasesAdaptiveAssessmentQuestionAttemptsTracker extends SimpleTracker
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_ADAPTIVE_ASSESSMENT_ITEM_ATTEMPT_ID = 'adaptive_assessment_item_attempt_id';
    const PROPERTY_COMPLEX_QUESTION_ID = 'complex_question_id';
    const PROPERTY_ANSWER = 'answer';
    const PROPERTY_FEEDBACK = 'feedback';
    const PROPERTY_SCORE = 'score';

    function validate_parameters(array $parameters = array())
    {
        $this->set_adaptive_assessment_item_attempt_id($parameters[self :: PROPERTY_ADAPTIVE_ASSESSMENT_ITEM_ATTEMPT_ID]);
        $this->set_complex_question_id($parameters[self :: PROPERTY_COMPLEX_QUESTION_ID]);
        $this->set_answer($parameters[self :: PROPERTY_ANSWER]);
        $this->set_feedback($parameters[self :: PROPERTY_FEEDBACK]);
        $this->set_score($parameters[self :: PROPERTY_SCORE]);
    }

    /**
     * Inherited
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(
                self :: PROPERTY_ADAPTIVE_ASSESSMENT_ITEM_ATTEMPT_ID,
                self :: PROPERTY_COMPLEX_QUESTION_ID,
                self :: PROPERTY_ANSWER,
                self :: PROPERTY_FEEDBACK,
                self :: PROPERTY_SCORE));
    }

    function get_adaptive_assessment_item_attempt_id()
    {
        return $this->get_default_property(self :: PROPERTY_ADAPTIVE_ASSESSMENT_ITEM_ATTEMPT_ID);
    }

    function set_adaptive_assessment_item_attempt_id($adaptive_assessment_item_attempt_id)
    {
        $this->set_default_property(self :: PROPERTY_ADAPTIVE_ASSESSMENT_ITEM_ATTEMPT_ID, $adaptive_assessment_item_attempt_id);
    }

    function get_complex_question_id()
    {
        return $this->get_default_property(self :: PROPERTY_COMPLEX_QUESTION_ID);
    }

    function set_complex_question_id($complex_question_id)
    {
        $this->set_default_property(self :: PROPERTY_COMPLEX_QUESTION_ID, $complex_question_id);
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
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}
?>