<?php

/**
 * Question builder for match text questions.
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class AssessmentMatchTextQuestionBuilder extends QuestionBuilder{
	
	static function factory($item, $settings){
		if(	!class_exists('AssessmentMatchTextQuestion') || 
			$item->has_templateDeclaration() ||
			count($item->list_interactions())!=1 ||
			!self::has_score($item)){
			return null;
		}
		
		$main = self::get_main_interaction($item);
		$is_text_entry = $main->is_extendedTextInteraction() || $main->is_textEntryInteraction();
		$is_numeric = self::is_numeric_interaction($item, $main);
		$has_answers = self::has_answers($item, $main);
		if(!$is_text_entry || $is_numeric || !$has_answers){
			return null;
		}
		return new self($settings);
	}
	
	public function create_question(){
		$result = new AssessmentMatchTextQuestion();
        return $result;
	}
		
	public function build(ImsXmlReader $item){
		$result = $this->create_question();
        $result->set_title($item->get_title());
        $result->set_description($this->get_question_text($item));
        
        $use_wildcards = false;
        $ignore_case = true;
		$interaction = self::get_main_interaction($item);
    	$answers = $this->get_possible_responses($item, $interaction);
    	foreach($answers as $answer){
    		//@todo: a better approach would be to generate an answer from the regex to get score, feedback, etc.
    		//currently general feedbacks - i.e. always true feedbacks - will not be imported
    		
    		//@todo: it would be better to check if the regex pattern is a wildcard pattern
    		//@todo: move is case sensitive and use wildcard from question to option?
    		//@todo: add regex has an option on top of wildcards for the question?
    		$use_wildcards = $use_wildcards || $this->is_regex($answer);
			$ignore_case = $ignore_case && !$this->is_case_sensitive($answer);
    		$value = $this->get_response_text($answer);
    		$score = $this->get_score($item, $interaction, $answer);
    		$feedback = $this->get_feedback($item, $interaction, $answer);
    		$option = new AssessmentMatchTextQuestionOption($value, $score, $feedback);
            $result->add_option($option);
    	}
		$result->set_use_wildcards($use_wildcards);
		$result->set_ignore_case($ignore_case);
		return $result;
	}
	
	protected function get_response_text($response){
		if(! $response instanceof ImsXmlReader){
			$result = $response;
		}else if($response->is_patternMatch()){
			$result = Wildcard::from_regex($response->pattern);
		}else{
			$result = $this->execute_formula($response);
		}
		
		return $result;
	}
	
	protected function is_regex($response){
		return $response instanceof ImsXmlReader && $response->is_patternMatch();
	}
	
	protected function is_case_sensitive($response){
		if(!is_object($response)){
			return true;
		}else if(! ($response instanceof ImsXmlReader)){
			return true;
		}else if($response->is_patternMatch()){
			return Wildcard::is_case_sensitive($response->pattern);
		}else if($response->is_stringMatch()){
			return $response->caseSensitive=='true';
		}else if($response->get_parent()->is_stringMatch()){
			return $response->get_parent()->caseSensitive=='true';		
		}else{
			return true;
		}
	}
	
}
