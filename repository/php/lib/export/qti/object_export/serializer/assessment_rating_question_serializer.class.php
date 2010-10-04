<?php

/**
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class AssessmentRatingQuestionSerializer extends QuestionSerializer{

	public static function factory($question, $target_root, $directory, $manifest, $toc){
		if($question instanceof AssessmentRatingQuestion){
			return new self($target_root, $directory, $manifest, $toc);
		}else{
			return null;
		}
	}
	
	protected function has_answer_feedback($question){
		return false;
	}
	
	protected function add_response_declaration(ImsQtiWriter $item, AssessmentRatingQuestion $question){
    	$declaration = $item->add_responseDeclaration(Qti::RESPONSE, Qti::CARDINALITY_SINGLE, Qti::BASETYPE_FLOAT);
    	$declaration->add_correctResponse()->add_value($question->get_correct());
        return $declaration;
	}

	protected function add_response_processing(ImsQtiWriter $item, $question){
		$result = $item->add_responseProcessing('http://www.imsglobal.org/question/qti_v2p0/rptemplates/match_correct');
		return $result;
	}
  	
	protected function add_interaction(ImsQtiWriter $body, $question){
        $low = $question->get_low();
        $high = $question->get_high();
		$result = $body->add_sliderInteraction(Qti::RESPONSE, $low, $high);
		return $result;
	}
}





?>