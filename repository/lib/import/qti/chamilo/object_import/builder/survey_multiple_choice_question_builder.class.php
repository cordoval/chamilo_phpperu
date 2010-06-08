<?php

/**
 * Question builder for Survey Multiple Choice Questions.
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class SurveyMultipleChoiceQuestionBuilder extends QuestionBuilder{
	
	static function factory($item, $source_root, $target_root, $category, $user, $log){
		if(	!class_exists('SurveyMultipleChoiceQuestion') || 
			$item->has_templateDeclaration() ||
			count($item->list_interactions())!=1 ||
			self::has_score($item)){
			return null;
		}
		$main = self::get_main_interaction($item);
		if(!$main->is_choiceInteraction()){
			return null;
		}
		
		$label = $main->label;
		$pairs = explode(';', $label);
		foreach($pairs as $pair){
			$entry = explode('=', $pair);
			if(count($entry)==2){
				$key = reset($entry);
				$value = trim($entry[1]);
				if($key=='display' && $value !='optionlist'){
					return false;
				}
			}
		}
		return new self($source_root, $target_root, $category, $user, $log);
	}
	
	public function create_question(){
		$result = new SurveyMultipleChoiceQuestion();
        return $result;
	}
	
	public function build(ImsXmlReader $item){
		$result = $this->create_question();
        $result->set_title($item->get_title());
        $result->set_description($this->get_question_text($item));
 		
		$interaction = self::get_main_interaction($item);
        $result->set_answer_type($interaction->maxChoices == 1 ? MultipleChoiceQuestion::ANSWER_TYPE_RADIO : MultipleChoiceQuestion::ANSWER_TYPE_CHECKBOX);
	
     	$choices = $interaction->all_simpleChoice();
    	
    	foreach($choices as $choice){       
			$title = $this->to_text($choice);
	        $option = new SurveyMultipleChoiceQuestionOption($title);
            $result->add_option($option);
    	}
    	
		return $result;
	}

}








