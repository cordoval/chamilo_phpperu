<?php
namespace repository\content_object\assessment_select_question;

use common\libraries\Path;

/**
 * $Id: assessment_select_question_option.class.php $
 * @package repository.lib.content_object.select_question
 */

/**
 * This class represents an option in a multiple choice question.
 */
class AssessmentSelectQuestionOption
{
    const PROPERTY_VALUE = 'value';
    const PROPERTY_SCORE = 'score';
    const PROPERTY_FEEDBACK = 'feedback';
    const PROPERTY_CORRECT = 'correct';

    private $value;
    private $correct;
    private $score;
    private $feedback;

    /**
     * Creates a new option for a multiple choice question
     * @param int $score The score of this answer in the question
     * @param string $feedback The feedback of this answer in the question
     */
    function __construct($value, $correct, $score, $feedback)
    {
        $this->value = $value;
        $this->correct = $correct;
        $this->score = $score;
        $this->feedback = $feedback;
    }

    /**
     * Gets the value of this option
     * @return string
     */
    function get_value()
    {
        return $this->value;
    }

    function get_feedback()
    {
        return $this->feedback;
    }

    /**
     * Gets the weight of this answer
     */
    function get_score()
    {
        return $this->score;
    }

    /**
     * Determines if this option is a correct answer
     * @return boolean
     */
    function is_correct()
    {
        return $this->correct;
    }

    function has_feedback()
    {
        return StringUtilities :: has_value($this->get_feedback(), true);
    }
}
?>