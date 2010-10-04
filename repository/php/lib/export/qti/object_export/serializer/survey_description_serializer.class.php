<?php

/**
 * Serializer for description "questions".
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class SurveyDescriptionSerializer extends QuestionSerializer{
	
	static function factory($question, $target_root, $directory, $manifest, $toc){
		if($question instanceof SurveyDescription){
			return new self($target_root, $directory, $manifest, $toc);
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








?>