<?php

/**
 * Question builder for Rating questions.
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class AssessmentRatingQuestionBuilder extends QuestionBuilder{
	
	static function factory($item, $source_root, $target_root, $category, $user, $log){
		if(	!class_exists('AssessmentRatingQuestion') || 
			$item->has_templateDeclaration() ||
			!self::has_score($item) ||
			count($item->list_interactions()) != 1){
				return null;
		}
		
		$main = self::get_main_interaction($item);
		if(! $main->is_sliderInteraction()){
			return null;
		}
		return new self($source_root, $target_root, $category, $user, $log);
	}
	
	public function create_question(){
		$result = new AssessmentRatingQuestion();
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
        $answers = $this->get_correct_responses($item, $interaction);
        $answer = reset($answers);
        $answer = $this->eval_answer($answer);
        $result->set_correct($answer);
		return $result;
	}
}


















