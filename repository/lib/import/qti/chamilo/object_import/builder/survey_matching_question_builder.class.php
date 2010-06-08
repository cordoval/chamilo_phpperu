<?php

/**
 * Question builder for Survey Matching Questions.
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class SurveyMatchingQuestionBuilder extends QuestionBuilder{
	
	static function factory($item, $source_root, $target_root, $category, $user, $log){
		if(	!class_exists('SurveyMatchingQuestion') || 
			$item->has_templateDeclaration() ||
			count($item->list_interactions())!=1 ||
			self::has_score($item)){
			return null;
		}
		$main = self::get_main_interaction($item);
		if(! $main->is_matchInteraction()){ 
			return null;
		}
		$sets = $main->list_simpleMatchSet();
		$start_set = reset($sets);
		$start_choices = $start_set->list_simpleAssociableChoice();
		foreach($start_choices as $start_choice){
			if($start_choice->matchMax != 1){
				return false;
			}
		} 
			
		return new self($source_root, $target_root, $category, $user, $log);
	}
	
	public function create_question(){
		$result = new SurveyMatchingQuestion();
        return $result;
	}

	protected function get_questions($item, $interaction){
		$result = array();
		$sets = $interaction->list_simpleMatchSet();
		if(count($sets)==0){//associateInteraction
			$result = $interaction->list_simpleAssociableChoice();
		}else if(count($sets)==1){//should not be the case
			$result = $sets[0]->list_simpleAssociableChoice();
		}else{ 
			$result = $sets[0]->list_simpleAssociableChoice();
		}
		return $result;
	}
	
	protected function get_answers($item, $interaction){
		$result = array();
		$sets = $interaction->list_simpleMatchSet();
		if(count($sets)==0){//associateInteraction
			$result = $interaction->list_simpleAssociableChoice();
		}else if(count($sets)==1){//should not be the case
			$result = $sets[0]->list_simpleAssociableChoice();
		}else{//matchInteraction
			$result = $sets[1]->list_simpleAssociableChoice();
		}
		return $result;
	}
	
	public function build(ImsXmlReader $item){
		$result = $this->create_question();
        $result->set_title($item->get_title());
        $result->set_description($this->get_question_text($item));
		$interaction = self::get_main_interaction($item);
		
    	$answers = $this->get_answers($item, $interaction);
    	foreach($answers as $answer){
            $result->add_match($this->to_html($answer));
    	}
    	
    	$questions = $this->get_questions($item, $interaction);
    	foreach($questions as $question){
	    	$question_text  = $this->to_html($question);
	        $option = new SurveyMatchingQuestionOption($question_text);
	        $result->add_option($option);
    	}
		return $result;
	}
}








