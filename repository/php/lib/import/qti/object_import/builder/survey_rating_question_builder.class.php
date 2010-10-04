<?php

/**
 * Question builder for Survey Rating Questions.
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class SurveyRatingQuestionBuilder extends QuestionBuilder{
	
	static function factory($item, $settings){
		if(	!class_exists('SurveyRatingQuestion') || 
			$item->has_templateDeclaration() ||
			self::has_score($item) ||
			count($item->list_interactions()) != 1){
				return null;
		}
		
		$main = self::get_main_interaction($item);
		if(! $main->is_sliderInteraction()){
			return null;
		}
		return new self($settings);
	}
	
	public function create_question(){
		$result = new SurveyRatingQuestion();
        return $result;
	}
	
	protected function eval_answer($answer){
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
		
        $result->set_low($interaction->lowerBound);
        $result->set_high($interaction->upperBound);
		return $result;
	}
}


















