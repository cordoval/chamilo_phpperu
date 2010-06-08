<?php
/**
 * $Id: assessment_multiple_choice_question_option.class.php  $
 * @package repository.lib.content_object.multiple_choice_question
 */
require_once PATH :: get_repository_path() . '/question_types/multiple_choice_question/multiple_choice_question_option.class.php';
/**
 * This class represents an option in a multiple choice question.
 */
class AssessmentMultipleChoiceQuestionOption extends MultipleChoiceQuestionOption
{
    const PROPERTY_CORRECT = 'correct';
	const PROPERTY_SCORE = 'score';
    const PROPERTY_FEEDBACK = 'feedback';
    
    private $score;
    private $feedback;
    
    function AssessmentMultipleChoiceQuestionOption($value, $correct, $score, $feedback)
    {
		parent :: MultipleChoiceQuestionOption($value);
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
}
?>