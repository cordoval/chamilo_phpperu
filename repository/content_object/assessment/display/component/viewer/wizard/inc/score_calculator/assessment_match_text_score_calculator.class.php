<?php
/**
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.score_calculator
 */
require_once dirname(__FILE__) . '/../score_calculator.class.php';

class AssessmentMatchTextScoreCalculator extends ScoreCalculator{

    function calculate_score(){
        $user_answers = $this->get_answer();
        $user_answer = trim($user_answers[0]);
        $question = $this->get_question();
        $use_wildcards = $question->get_use_wildcards();
        $ignore_case = $question->get_ignore_case();
        $max_score = $this->max_score($question);
        $options = $question->get_options();
        
        $result = 0;
        foreach ($options as $option){
        	if($this->match($user_answer, $option->get_value(), $ignore_case, $use_wildcards)){
                $option_score = $this->make_score_relative($option->get_score(), $max_score);
                $result = max($result, $option_score);
            }
        }
        return $result;
    }
    
    protected function match($user_answer, $value, $ignore_case, $use_wildcards){
    	if($use_wildcards){
    		$star = '__star__';
    		$value = str_replace('*', $star, $value);
    		$value = preg_quote($value);
    		$value = str_replace($star, '.*', $value);
    		$value = "/$value/" . ($ignore_case ? 'i' : '');
    		return preg_match($value, $user_answer)>0;
    	}else{
    		if($ignore_case){
    			return strtolower($user_answer) == strtolower($value);
    		}else{
    			return $user_answer == $value;
    		}
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




