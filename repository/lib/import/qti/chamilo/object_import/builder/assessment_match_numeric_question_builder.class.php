<?php

/**
 * Question builder for match numeric questions.
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class AssessmentMatchNumericQuestionBuilder extends QuestionBuilder{
	
	static function factory($item, $source_root, $target_root, $category, $user, $log){
		if(	!class_exists('AssessmentMatchNumericQuestion') || 
			$item->has_templateDeclaration() ||
			count($item->list_interactions())>1 ||
			!self::has_score($item)){
			return null;
		}
		$main = self::get_main_interaction($item);
		if(	! self::is_numeric_interaction($item, $main) || 
			! self::has_answers($item, $main)){
			return null;
		}
		return new self($source_root, $target_root, $category, $user, $log);
	}
	
	public function create_question(){
		$result = new AssessmentMatchNumericQuestion();
        return $result;
	}
	
	protected function get_answer($answer){
		if($this->is_formula($answer)){
			return $this->execute_formula($answer);
		}else{
			return $answer;
		}
	}
		
	public function build(ImsXmlReader $item){
		$result = $this->create_question();
        $result->set_title($item->get_title());
        $result->set_description($this->get_question_text($item));
		$interaction = self::get_main_interaction($item);
    	$answers = $this->get_possible_responses($item, $interaction);
    	foreach($answers as $answer){
    		$value = $this->get_answer($answer);
    		$score = $this->get_score($item, $interaction, $answer);
    		$tolerance = $this->get_tolerance($item, $interaction, $answer);
    		$tolerance_type = $this->get_tolerance_type($item, $interaction, $answer);
    		//if($tolerance_type == Qti::TOLERANCE_MODE_RELATIVE){
    		//	$tolerance = $tolerance / 100 * $value;
    		//}
    		$feedback = $this->get_feedback($item, $interaction, $answer);
    		$option = new AssessmentMatchNumericQuestionOption($value, $tolerance, $score, $feedback);
            $result->add_option($option);
    	}
        $result->set_tolerance_type($this->get_question_tolerance_type($item));
    	
		return $result;
	}
	
	protected function get_question_tolerance_type($item){
		$interaction = self::get_main_interaction($item);
    	$answers = $this->get_possible_responses($item, $interaction);
    	foreach($answers as $answer){
    		$tolerance_type = $this->get_tolerance_type($item, $interaction, $answer);
    		if($tolerance_type != Qti::TOLERANCE_MODE_RELATIVE){
    			return AssessmentMatchNumericQuestion::TOLERANCE_TYPE_ABSOLUTE;
    		}
    	}
    	return AssessmentMatchNumericQuestion::TOLERANCE_TYPE_RELATIVE;
	}
}








