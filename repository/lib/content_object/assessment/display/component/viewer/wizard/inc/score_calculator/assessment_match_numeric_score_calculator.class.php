<?php
/**
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.score_calculator
 */
require_once dirname(__FILE__) . '/../score_calculator.class.php';

class AssessmentMatchNumericScoreCalculator extends ScoreCalculator
{

    function calculate_score(){
        $user_answers = $this->get_answer();
        $user_answer = trim($user_answers[0]);
        if(! is_numeric($user_answer)){
        	return 0;
        }
        $question = $this->get_question();
        $tolerance_type = $question->get_tolerance_type();
        $max_score = $this->max_score($question);
        $options = $question->get_options();
        
        $result = 0;
        foreach ($options as $option){
        	if($this->match($user_answer, $option->get_value(), $option->get_tolerance(), $tolerance_type)){
                $option_score = $this->make_score_relative($option->get_score(), $max_score);
                $result = max($result, $option_score);
            }
        }
        
        return $result;
    }
    
    protected function match($user_answer, $value, $tolerance, $tolerance_type){
        switch($tolerance_type){
        	case AssessmentMatchNumericQuestion::TOLERANCE_TYPE_ABSOLUTE:
        		$min = $value - abs($tolerance);
        		$max = $value + abs($tolerance);
        		return $min <= $user_answer && $user_answer <= $max;
        		
        	case AssessmentMatchNumericQuestion::TOLERANCE_TYPE_RELATIVE:
        		$min = $value - abs($value*$tolerance);
        		$max = $value + abs($value*$tolerance);
        		return $min <= $user_answer && $user_answer <= $max;
        		
        	default:
        		throw new Exception('Unknown tolerance type');
        }
    }
    
    protected function max_score($question){
    	$result = 0;
    	$options = $question->get_options();
    	foreach($options as $option){
    		$result = max($result, $option->get_score());
    	}
    	return $result;
    }
}






?>