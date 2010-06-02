<?php

/**
 * Question builder for Survey Select Questions.
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class SurveySelectQuestionBuilder extends QuestionBuilder{
	
	static function factory($item, $source_root, $target_root, $category, $user, $log){
		if(	!class_exists('SurveySelectQuestion') || 
			$item->has_templateDeclaration() ||
			count($item->list_interactions())!=1 ||
			self::has_score($item)){
			return null;
		}
		$main = self::get_main_interaction($item);
		if(! $main->is_choiceInteraction()){ 
			return null;
		}
		
		$label = $main->label;
		$pairs = explode(';', $label);
		foreach($pairs as $pair){
			$entry = explode('=', $pair);
			if(count($entry)==2){
				$key = reset($entry);
				$value = trim($entry[1]);
				if($key=='display' && $value !='listbox'){
					return false;
				}
			}
		}
		
		return new self($source_root, $target_root, $category, $user, $log);
	}
	
	public function create_question(){
		$result = new SurveySelectQuestion();
        return $result;
	}
	
	protected function get_answer_type($item){
		$interaction = self::get_main_interaction($item);
		//@todo: make that constants
		$result = $interaction->maxChoices == 1 ? 'radio' : 'checkbox';
		return $result;
	}
	
	public function build(ImsXmlReader $item){
		$result = $this->create_question();
        $result->set_title($item->get_title());
        $result->set_description($this->get_question_text($item));
        $result->set_answer_type($this->get_answer_type($item));
        
		$interaction = self::get_main_interaction($item);
		
		$choices = $interaction->list_simpleChoice();
    	foreach($choices as $choice){
    		$value = $this->to_text($choice);
    		$option = new SurveySelectQuestionOption($value);
            $result->add_option($option);
    	}
		return $result;
	}
}








