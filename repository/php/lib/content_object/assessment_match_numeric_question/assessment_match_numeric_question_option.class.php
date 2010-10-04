<?php

/**
  * @package repository.lib.content_object.match_numeric_question
 */
require_once dirname(__FILE__) . '/main.php'; 

/**
 * This class represents an option in a matching question.
 */
class AssessmentMatchNumericQuestionOption
{
	const PROPERTY_TOLERANCE = 'tolerance';

    private $value;
    private $tolerance;    
    private $score;
    private $feedback;

    public function __construct($value, $tolerance, $score, $feedback){
    	$this->value = $value;
        $this->tolerance = $tolerance;
        $this->score = $score;
        $this->feedback = $feedback;
    }
    
    public function get_value(){
    	return $this->value;
    }
    
    public function get_tolerance(){
    	return $this->tolerance;
    }

    public function get_score(){
        return $this->score;
    }

    public function get_feedback(){
        return $this->feedback;
    }
    
}
?>