<?php

/**
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class SurveyRatingQuestionSerializer extends QuestionSerializer{

	public static function factory($question, $target_root, $manifest){
		if($question instanceof SurveyRatingQuestion){
			return new self($target_root, $manifest);
		}else{
			return null;
		}
	}
	
	protected function has_answer_feedback($question){
		return false;
	}

	protected function add_response_processing(ImsQtiWriter $item, $question){
		return null;
	}
	
	protected function add_score_declaration(ImsQtiWriter $item, $question){
		return null;
	}
	
	protected function add_response_declaration(ImsQtiWriter $item, SurveyRatingQuestion $question){
    	$declaration = $item->add_responseDeclaration(Qti::RESPONSE, Qti::CARDINALITY_SINGLE, Qti::BASETYPE_FLOAT);
        return $declaration;
	}
  	
	protected function add_interaction(ImsQtiWriter $body, SurveyRatingQuestion $question){
        $low = $question->get_low();
        $high = $question->get_high();
		$result = $body->add_sliderInteraction(Qti::RESPONSE, $low, $high);
		return $result;
	}
}





