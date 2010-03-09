<?php
/**
 * $Id: assessment_select_question_option.class.php $
 * @package repository.lib.content_object.select_question
 */
require_once PATH :: get_repository_path() . '/question_types/select_question/select_question_option.class.php';

/**
 * This class represents an option in a multiple choice question.
 */
class AssessmentSelectQuestionOption extends SelectQuestionOption
{
    const PROPERTY_SCORE = 'score';
    const PROPERTY_FEEDBACK = 'feedback';
    const PROPERTY_CORRECT = 'correct';
    
    private $correct;
    private $score;
    private $feedback;

    /**
     * Creates a new option for a multiple choice question
     * @param int $score The score of this answer in the question
     * @param string $feedback The feedback of this answer in the question
     */
    function AssessmentSelectQuestionOption($value, $correct, $score, $feedback){
    	parent :: SelectQuestionOption($value);
    	$this->correct = $correct;
    	$this->score = $score;
    	$this->correct= $feedback;   	
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
}
?>