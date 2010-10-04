<?php

/**
 * Serializer for matrix questions.
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class SurveyMatrixQuestionSerializer extends QuestionSerializer{
	
	static function factory($question, $target_root, $directory, $manifest, $toc){
		if($question instanceof SurveyMatrixQuestion){
			return new self($target_root, $directory, $manifest, $toc);
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

	protected function add_response_declaration(ImsQtiWriter $item, SurveyMatrixQuestion $question){
		$id = Qti::RESPONSE;
		$cardinality =  Qti::CARDINALITY_MULTIPLE;
		$type = Qti::BASETYPE_DIRECTEDPAIR;
		$result = $item->add_responseDeclaration($id, $cardinality, $type);
		return $result;
	}
	
	protected function add_interaction(ImsQtiWriter $body, $question){
		$result = $body->add_matchInteraction(ImsQtiWriter::RESPONSE, 0, true);
		$questions = $result->add_simpleMatchSet();
		$answers = $result->add_simpleMatchSet();
		
        $options = $question->get_options();
		foreach($options as $index => $option){
			$question_id = "Q_$index";
			$text = $this->translate_text($option->get_value());
			//@todo: check if one question can map to one answer or multiple
			$choice = $questions->add_simpleAssociableChoice($question_id, false, array(), 1)->add_flow($text);
		}
		
		$matches = $question->get_matches();
		foreach($matches as $index => $match){
			$answer_id = "A_$index";
			$text = $this->translate_text($match);
			$answers->add_simpleAssociableChoice($answer_id, false, array(), 0)->add_flow($text);
		}
		return $result;
	}
}








?>