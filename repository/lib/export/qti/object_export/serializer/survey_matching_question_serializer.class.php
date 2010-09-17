<?php
require_once Path::get_repository_path(). 'lib/content_object/assessment_matching_question/assessment_matching_question_option.class.php';
 
/**
 * Serializer for matching questions.
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class SurveyMatchingQuestionSerializer extends QuestionSerializer{
	
	static function factory($question, $target_root, $directory, $manifest, $toc){
		if($question instanceof SurveyMatchingQuestion){
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

	protected function add_response_declaration(ImsQtiWriter $item, SurveyMatchingQuestion $question){
		$id = Qti::RESPONSE;
		$cardinality =  Qti::CARDINALITY_MULTIPLE;
		$type = Qti::BASETYPE_DIRECTEDPAIR;
		$result = $item->add_responseDeclaration($id, $cardinality, $type);
	}
	
	protected function add_interaction(ImsQtiWriter $body, $question){
        $options = $question->get_options();
		$question_count = count($options);
		
		$result = $body->add_matchInteraction(ImsQtiWriter::RESPONSE, $question_count, true);
		$questions = $result->add_simpleMatchSet();
		$answers = $result->add_simpleMatchSet();
		
		foreach($options as $index => $option){
			$question_id = "Q_$index";
			$text = $this->translate_text($option->get_value());
			$choice = $questions->add_simpleAssociableChoice($question_id, false, array(), 1)->add_flow($text);
		}
		
		$matches = $question->get_matches();
		foreach($matches as $index => $match){
			$answer_id = "A_$index";
			$text = $this->translate_text($match);
			$answers->add_simpleAssociableChoice($answer_id, false, array(), $question_count)->add_flow($text);
		}
		return $result;
	}
}








?>