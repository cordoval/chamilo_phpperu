<?php

/**
 * Serializer for open questions.
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class SurveyOpenQuestionSerializer extends QuestionSerializer{
	
	static function factory($question, $target_root, $manifest){
		if($question instanceof SurveyOpenQuestion){
			return new self($target_root, $manifest);
		}else{
			return null;
		}
	}
	
	protected function has_answer_feedback($question){
		return false;
	}
	
	protected function add_response_processing($item, $question){
		return null;
	}
	
	protected function add_score_declaration(ImsQtiWriter $item, $question){
		return null;
	}
	
	protected function add_response_declaration(ImsQtiWriter $item, $question){
		$item->add_responseDeclaration(Qti::RESPONSE, Qti::CARDINALITY_SINGLE, Qti::BASETYPE_STRING);
	}
	
	protected function add_interaction(ImsQtiWriter $body, ContentObject $question){
		$body->add_extendedTextInteraction(Qti::RESPONSE, 800, 10);
	}
}








