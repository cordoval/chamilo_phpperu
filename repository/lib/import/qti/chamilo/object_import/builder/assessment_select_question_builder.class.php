<?php

/**
 * Question builder for Assessment Select Questions.
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class AssessmentSelectQuestionBuilder extends QuestionBuilder{
	
	static function factory($item, $source_root, $target_root, $category, $user, $log){
		if(	!class_exists('AssessmentSelectQuestion') || 
			$item->has_templateDeclaration() ||
			count($item->list_interactions())!=1 ||
			!self::has_score($item)){
			return null;
		}
		$main = self::get_main_interaction($item);
		if(! $main->is_choiceInteraction()){ 
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
					if($key=='display' && $value !='listbox'){
						return false;
					}
				}
			}
		}
		
		return new self($source_root, $target_root, $category, $user, $log);
	}
	
	public function create_question(){
		$result = new AssessmentSelectQuestion();
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
		$max_individual_score = 0;
    	foreach($choices as $choice){
    		$answer = $choice->identifier;
    		$score = $this->get_score($item, $interaction, $answer);
    		$max_individual_score = max($max_individual_score, $score);
    	}
    	foreach($choices as $choice){
    		$answer = $choice->identifier;
    		$value = $this->to_text($choice);
    		$score = $this->get_score($item, $interaction, $answer);
    		$feedback = $this->get_feedback($item, $interaction, $answer);
    		$correct = $max_individual_score == $score;
    		$option = new AssessmentSelectQuestionOption($value, $correct, $score, $feedback);
            $result->add_option($option);
    	}
		return $result;
	}
}








