<?php
namespace repository\content_object\assessment_matching_question;

use common\libraries\Path;

/**
 * $Id: assessment_matching_question_option.class.php  $
 * @package repository.lib.content_object.matching_question
 */

/**
 * This class represents an option in a matching question.
 */
class AssessmentMatchingQuestionOption
{
    const PROPERTY_VALUE = 'value';
    const PROPERTY_SCORE = 'score';
    const PROPERTY_FEEDBACK = 'feedback';
    const PROPERTY_MATCH = 'match';

    private $value;
    private $score;
    private $feedback;
    private $match;

    /**
     * Creates a new option for a matching question
     * @param string $value The value of the option
     * @param int $match The index of the match corresponding to this option
     * @param int $score The score of this answer in the question
     */
    function __construct($value, $match, $score, $feedback)
    {
        $this->value = $value;
        $this->score = $score;
        $this->feedback = $feedback;
        $this->match = $match;
    }

    /**
     * Gets the value of this option
     * @return string
     */
    function get_value()
    {
        return $this->value;
    }

    /**
     * Gets the score of this answer
     */
    function get_score()
    {
        return $this->score;
    }

    function get_feedback()
    {
        return $this->feedback;
    }

    /**
     * Gets the index of the match corresponding to this option
     * @return int
     */
    function get_match()
    {
        return $this->match;
    }
}
?>