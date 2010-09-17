<?php

/**
 * Question builder for Assessment Matrix Questions.
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class AssessmentMatrixQuestionBuilder extends QuestionBuilder{
	
	static function factory($item, $settings){
		if(	!class_exists('AssessmentMatrixQuestion') || 
			$item->has_templateDeclaration() ||
			count($item->list_interactions())!=1 ||
			!self::has_score($item)){
			return null;
		}
		$main = self::get_main_interaction($item);
		if(! $main->is_matchInteraction()){ 
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
					if($key=='display' && $value !='matrix'){
						return false;
					}
				}
			}
		}
		
		return new self($settings);
	}
	
	public function create_question(){
		$result = new AssessmentMatrixQuestion();
        return $result;
	}
	
	public function get_matrix_type($item){
		$interaction = self::get_main_interaction($item);
		$sets = $interaction->list_simpleMatchSet();
		$start_set = reset($sets);
		$start_choices = $start_set->list_simpleAssociableChoice();
		foreach($start_choices as $start_choice){
			if($start_choice->matchMax != 1){
				return MatrixQuestion::MATRIX_TYPE_CHECKBOX;
			}
		} 
		return MatrixQuestion::MATRIX_TYPE_RADIO;
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
        $result->set_matrix_type($this->get_matrix_type($item));
		$interaction = self::get_main_interaction($item);
		
    	$answers = $this->get_answers($item, $interaction);
    	$index = 0;
    	foreach($answers as $answer){
            $result->add_match($this->to_html($answer));
    		$answer->index = $index++;
    	}
    	
    	$questions = $this->get_questions($item, $interaction);
    	foreach($questions as $question){
	    	$question_text  = $this->to_html($question);
	    	$question_answers = array();
    		foreach($answers as $answer){
	    		$response = $question->identifier . ' ' . $answer->identifier;
		        $score = $this->get_score($item, $interaction, $response);
		        if($score>0){
		        	$question_answers[] = $answer;
		        }
    		}
    		$matches = array();
    		$response = array();
    		foreach($question_answers as $answer){
    			$response[] = $question->identifier . ' ' . $answer->identifier;
    			$matches[$answer->index] = $answer->index;
    		}
    		$matches = serialize($matches); //@todo: move serialize to questionOption?
    		$question_feedbacks = $this->get_children_feedbacks($item, $interaction, $response, $question);
    		$modal_feedbacks = $this->get_modal_feedbacks($item, $interaction, $response);
    		$feedback = implode('<br/>', array_merge($modal_feedbacks, $question_feedbacks));
    				
		    $score = round($this->get_score($item, $interaction, $response), 2);
	        $option = new AssessmentMatrixQuestionOption($question_text, $matches, $score, $feedback);
	        $result->add_option($option);
    	}
		return $result;
	}
}











