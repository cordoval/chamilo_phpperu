<?php
/**
 * $Id: assessment_matrix_question_option.class.php $
 * @package repository.lib.content_object.matrix_question
 */
require_once PATH :: get_repository_path() . '/question_types/matrix_question/matrix_question_option.class.php';

/**
 * This class represents an option in a matrix question.
 */
class AssessmentMatrixQuestionOption extends MatrixQuestionOption
{
	const PROPERTY_SCORE = 'score';
	const PROPERTY_FEEDBACK = 'feedback';
	const PROPERTY_MATCHES = 'matches';

    private $score;
    private $feedback;
    private $matches;

    /**
     * Creates a new option for a matrix question
     * @param string $value The value of the option
     * @param int $match The index of the match corresponding to this option
     * @param int $score The score of this answer in the question
     */
    function AssessmentMatrixQuestionOption($value = '', $matches = array(), $score = 1, $feedback = '')
    {
		parent :: MatrixQuestionOption($value);
        $this->score = $score;
        $this->comment = $feedback;
        $this->matches = $matches;
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
    function get_matches()
    {
        return unserialize($this->matches);
    }
}
?>