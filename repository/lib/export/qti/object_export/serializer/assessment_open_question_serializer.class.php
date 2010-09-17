<?php

/**
 * Serializer for open questions.
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class AssessmentOpenQuestionSerializer extends QuestionSerializer{
	
	static function factory($question, $target_root, $directory, $manifest, $toc){
		if($question instanceof AssessmentOpenQuestion){
			return new self($target_root, $directory, $manifest, $toc);
		}else{
			return null;
		}
	}
	
	protected function has_answer_feedback($question){
		return false;
	}
	
	protected function add_response_declaration(ImsQtiWriter $item, $question){
		$type = $question->get_question_type();
		if($type == AssessmentOpenQuestion::TYPE_DOCUMENT){
			$item->add_responseDeclaration(Qti::RESPONSE, Qti::CARDINALITY_SINGLE, Qti::BASETYPE_FILE);
		}else if($type == AssessmentOpenQuestion::TYPE_OPEN){
			$item->add_responseDeclaration(Qti::RESPONSE, Qti::CARDINALITY_SINGLE, Qti::BASETYPE_STRING);
		}else if($type == AssessmentOpenQuestion::TYPE_OPEN_WITH_DOCUMENT){
			$item->add_responseDeclaration(Qti::RESPONSE, Qti::CARDINALITY_SINGLE, Qti::BASETYPE_STRING);
			$item->add_responseDeclaration('UPLOAD', Qti::CARDINALITY_SINGLE, Qti::BASETYPE_FILE);
		}
	}
	
	protected function add_interaction(ImsQtiWriter $body, ContentObject $question){
		$type = $question->get_question_type();
		if($type == AssessmentOpenQuestion::TYPE_DOCUMENT){
		 	$body->add_uploadInteraction();
		}else if($type == AssessmentOpenQuestion::TYPE_OPEN){
		 	$body->add_extendedTextInteraction(Qti::RESPONSE, 800, 10);
		}else if($type == AssessmentOpenQuestion::TYPE_OPEN_WITH_DOCUMENT){
		 	$body->add_extendedTextInteraction(Qti::RESPONSE, 800, 10);
		 	$body->add_uploadInteraction('UPLOAD');
		}else{
			throw new Exception("Unknown document type: $type");
		}
	}
	
	protected function add_response_processing($item, $question){
		return false;
	}
}








?>