<?php

/**
 * Serializer for description "questions".
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class SurveyDescriptionSerializer extends QuestionSerializer{
	
	static function factory($question, $target_root, $manifest){
		if($question instanceof SurveyDescription){
			return new self($target_root);
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
		return null;
	}
	
	protected function add_interaction(ImsQtiWriter $body, ContentObject $question){
		return null;
	}
}








