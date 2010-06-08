<?php
/**
 * $Id: assessment_matching_question_option.class.php  $
 * @package repository.lib.content_object.matching_question
 */
require_once PATH :: get_repository_path() . '/question_types/matching_question/matching_question_option.class.php';

/**
 * This class represents an option in a matching question.
 */
class AssessmentMatchingQuestionOption extends MatchingQuestionOption
{
	const PROPERTY_SCORE = 'score';
	const PROPERTY_FEEDBACK = 'feedback';
	const PROPERTY_MATCH = 'match';

	private $score;
    private $feedback;
    private $match;

    /**
     * Creates a new option for a matching question
     * @param string $value The value of the option
     * @param int $match The index of the match corresponding to this option
     * @param int $score The score of this answer in the question
     */
    function AssessmentMatchingQuestionOption($value, $match, $score, $feedback)
    {
        parent :: MatchingQuestionOption($value);
        $this->score = $score;
        $this->feedback = $feedback;
        $this->match = $match;
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