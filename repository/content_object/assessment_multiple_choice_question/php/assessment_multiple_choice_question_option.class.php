<?php
namespace repository\content_object\assessment_multiple_choice_question;

use common\libraries\Path;

/**
 * $Id: assessment_multiple_choice_question_option.class.php  $
 * @package repository.lib.content_object.multiple_choice_question
 */
/**
 * This class represents an option in a multiple choice question.
 */
class AssessmentMultipleChoiceQuestionOption
{
    const PROPERTY_VALUE = 'value';
    const PROPERTY_CORRECT = 'correct';
    const PROPERTY_SCORE = 'score';
    const PROPERTY_FEEDBACK = 'feedback';

    private $value;
    private $score;
    private $feedback;

    function __construct($value, $correct, $score, $feedback)
    {
        $this->value = $value;
        $this->correct = $correct;
        $this->score = $score;
        $this->feedback = $feedback;
    }

    function get_feedback()
    {
        return $this->feedback;
    }

    function get_score()
    {
        return $this->score;
    }

    function is_correct()
    {
        return $this->correct;
    }

    function get_value()
    {
        return $this->value;
    }
}
?>