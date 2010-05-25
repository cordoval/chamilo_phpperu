<?php

/**
  * @package repository.lib.content_object.match_text_question
 */
require_once dirname(__FILE__) . '/main.php'; 

/**
 * This class represents an option in a match tex question.
 */
class AssessmentMatchTextQuestionOption{

    private $value;   
    private $score;
    private $feedback;

    public function __construct($value, $score, $feedback){
    	$this->value = $value;
        $this->score = $score;
        $this->feedback = $feedback;
    }
    
    public function get_value(){
    	return $this->value;
    }
    
    public function get_score(){
        return $this->score;
    }

    public function get_feedback(){
        return $this->feedback;
    }
    
}