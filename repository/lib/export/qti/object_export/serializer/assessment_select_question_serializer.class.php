<?php

require_once Path::get_repository_path(). 'lib/content_object/assessment_select_question/assessment_select_question_option.class.php';

/**
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class AssessmentSelectQuestionSerializer extends QuestionSerializer{

	public static function factory($question, $target_root, $manifest){
		if($question instanceof AssessmentSelectQuestion){
			return new self($target_root, $manifest);
		}else{
			return null;
		}
	}
	       
    protected function get_question_score(AssessmentSelectQuestion $question){
    	$single_answer = $question->get_answer_type() == 'radio';
        $answers = $question->get_options();
    	if($single_answer){
    		$max = 0;
	        foreach($answers as $answer){
	        	$max = max($max, $answer->get_score());
	        }
	        $result = $max;
    	}else{
    		$result = 0;
    		foreach($answers as $answer){
    			if($answer->get_score()>=0){
    				$result += $answer->get_score();
    			}
    		}
    	}
    	return $result;
    }
	
	protected function has_answer_feedback($question){
		return true;
	}
	
	protected function add_response_declaration(ImsQtiWriter $item, $question){
    	$cardinality = $question->get_answer_type() == 'radio' ? Qti::CARDINALITY_SINGLE : Qti::CARDINALITY_MULTIPLE;
    	$maximum_score = $this->get_question_score($question);
    	$declaration = $item->add_responseDeclaration(Qti::RESPONSE, $cardinality, Qti::BASETYPE_IDENTIFIER);
    	$correct = $declaration->add_correctResponse();
    	$mapping = $declaration->add_mapping(0, $maximum_score, 0);
    	
        $answers = $question->get_options();
        foreach($answers as $index => $answer){
        	$id = "ID_$index";
        	$score = $answer->get_score();
        	if($answer->is_correct()){
        		$correct->add_value($id);
        	}
        	$mapping->add_mapEntry($id, $score);
        }
        return $declaration;
	}
	
  	protected function add_score_processing(ImsQtiWriter $response_processing, $question){
    	return $response_processing->add_standard_response_map_response();
  	}
  	
	protected function add_interaction(ImsQtiWriter $body, $question){
		$max_choices = $question->get_answer_type() == 'radio' ? 1 : 0;
		$shuffle = true;
		$label = 'display=listbox';
		$result = $body->add_choiceInteraction(Qti::RESPONSE, $max_choices, $shuffle, '', '', '', $label);
        $answers = $question->get_options();
        foreach($answers as $index => $answer){
        	$id = "ID_$index";
        	$text = $this->translate_text($answer->get_value());
        	$feedback = $this->translate_text($answer->get_feedback());
			$choice = $result->add_simpleChoice($id);
			$choice->add_flow($text);
			$choice->add_feedbackInline(Qti::FEEDBACK, $id, Qti::FEEDBACK_SHOW)->add_flow($feedback);
        }
		return $result;
	}
}
?>