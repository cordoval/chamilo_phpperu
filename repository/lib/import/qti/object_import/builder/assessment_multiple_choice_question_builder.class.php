<?php

/**
 * Question builder for Assessment Multiple Choice Questions.
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class AssessmentMultipleChoiceQuestionBuilder extends QuestionBuilder{
	
	static function factory($item, $settings){
		if(	!class_exists('AssessmentMultipleChoiceQuestion') || 
			$item->has_templateDeclaration() ||
			count($item->list_interactions())!=1 ||
			!self::has_score($item)){
			return null;
		}
		$main = self::get_main_interaction($item);
		if(!$main->is_choiceInteraction()){
			return null;
		}
	
		if($item->toolName == Qti::get_tool_name()){
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
		}
		return new self($settings);
	}
	
	public function create_question(){
		$result = new AssessmentMultipleChoiceQuestion();
        return $result;
	}
	
	public function build(ImsXmlReader $item){
		$result = $this->create_question();
        $result->set_title($item->get_title());
        $result->set_description($this->get_question_text($item));
 		
		$interaction = self::get_main_interaction($item);
        $result->set_answer_type($interaction->maxChoices == 1 ? MultipleChoiceQuestion::ANSWER_TYPE_RADIO : MultipleChoiceQuestion::ANSWER_TYPE_CHECKBOX);
	
		$max_individual_score = 0;
     	$choices = $interaction->all_simpleChoice();
    	foreach($choices as $choice){
    		$answer = $choice->identifier;
    		$score = $this->get_score($item, $interaction, $answer);
    		$max_individual_score = max($max_individual_score, $score);
    	}
    	
    	foreach($choices as $choice){            
    		$answer = $choice->identifier;
			$title = $this->to_text($choice);
	        $feedback = $this->get_feedback($item, $interaction, $answer);
	        
	        $score = $this->get_score($item, $interaction, $answer);
    		$correct = $max_individual_score == $score;
	        $option = new AssessmentMultipleChoiceQuestionOption($title, $correct, $score, $feedback);
            $result->add_option($option);
    	}
    	
		return $result;
	}

}








