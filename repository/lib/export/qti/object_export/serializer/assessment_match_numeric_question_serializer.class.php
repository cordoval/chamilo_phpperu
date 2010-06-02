<?php

/**
 * Serializer for Assessment Match Numeric Questions. 
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */

class AssessmentMatchNumericQuestionSerializer extends QuestionSerializer{
	
	static function factory($question, $target_root, $manifest){
		if($question instanceof AssessmentMatchNumericQuestion){
			return new self($target_root, $manifest);
		}else{
			return null;
		}
	}
	
	protected function has_answer_feedback($question){
		return true;
	}
	
    protected function get_question_score(AssessmentMatchNumericQuestion $question){
    	$result = 0;
        $answers = $question->get_options();
        foreach($answers as $answer){
        	$result = max($result, $answer->get_score());
        }
    	return $result;
    }
	
	protected function add_response_declaration(ImsQtiWriter  $item, $question){
		$result = $item->add_responseDeclaration(Qti::RESPONSE, Qti::CARDINALITY_SINGLE, Qti::BASETYPE_FLOAT);
		$correct_response = $result->add_correctResponse();
    	$maximum_score = $this->get_question_score($question);
			
        $answers = $question->get_options();
        foreach($answers as $index => $answer){
        	if($answer->get_score() == $maximum_score){
        		$correct_response->add_value($answer->get_value());
        	}
        }
		return $result;
	}
	
	protected function add_score_processing(ImsQtiWriter $response_processing, AssessmentMatchNumericQuestion $question){
		$result = $response_processing->add_responseCondition();
		$response_id = Qti::RESPONSE;
		$outcome_id = Qti::SCORE;
    	$if = $result->add_responseIf();
    	$if->add_isNull()->add_variable($response_id);
    	$if->add_setOutcomeValue($outcome_id)->add_baseValue(Qti::BASETYPE_FLOAT, 0);
    	
    	$answers = $question->get_options();
    	foreach($answers as $answer){
    		$score = $answer->get_score();
    		$tolerance_mode = $question->get_tolerance_type() == AssessmentMatchNumericQuestion::TOLERANCE_TYPE_ABSOLUTE ? Qti::TOLERANCE_MODE_ABSOLUTE : Qti::TOLERANCE_MODE_RELATIVE;
    		$elseif = $result->add_responseElseIf();
	    	$equal = $elseif->add_equal($tolerance_mode, $answer->get_tolerance(), $answer->get_tolerance());
	    	$equal->add_baseValue(Qti::BASETYPE_FLOAT, $answer->get_value());
	    	$equal->add_variable($response_id); 
	    	$elseif->add_setOutcomeValue($outcome_id)->add_baseValue(Qti::BASETYPE_FLOAT, $score);
	 	}
    	$else = $result->add_responseElse();
    	$else->add_setOutcomeValue($outcome_id)->add_baseValue(Qti::BASETYPE_FLOAT, 0);
		return $result;
	}
	
	protected function add_answer_feedback_processing($response_processing, $question){
		$response_id = Qti::RESPONSE;
		$outcome_id = Qti::FEEDBACK;
		$result = $response_processing->add_responseCondition();
    	$if = $result->add_responseIf();
    	$if->add_isNull()->add_variable($response_id);
    	$if->add_setOutcomeValue($outcome_id)->add_baseValue(Qti::BASETYPE_IDENTIFIER, 'DEFAULT_FEEDBACK');
    	$count = 0;
    	$answers = $question->get_options();
    	foreach($answers as $answer){
    		$id = 'FEEDBACK_ID_' . ++$count;
   			$tolerance_mode = $question->get_tolerance_type() == AssessmentMatchNumericQuestion::TOLERANCE_TYPE_ABSOLUTE ? Qti::TOLERANCE_MODE_ABSOLUTE : Qti::TOLERANCE_MODE_RELATIVE;
    		$elseif = $result->add_responseElseIf();
	    	$equal = $elseif->add_equal($tolerance_mode, $answer->get_tolerance(), $answer->get_tolerance());
	    	$equal->add_baseValue(Qti::BASETYPE_FLOAT, $answer->get_value());
	    	$equal->add_variable($response_id); 
    		$elseif->add_setOutcomeValue($outcome_id)->add_baseValue(Qti::BASETYPE_IDENTIFIER, $id);
	 	}
	 	$else = $result->add_responseElse();
    	$else->add_setOutcomeValue($outcome_id)->add_baseValue(Qti::BASETYPE_IDENTIFIER, 'DEFAULT_FEEDBACK');
	 
		return $result;
	}
	
	protected function add_answer_feedback(ImsQtiWriter $item, $question){   
		$count = 0;
        $answers = $question->get_options();
		foreach($answers as $answer){
			++$count;
    		$id = 'FEEDBACK_ID_'. $count;
    		$feedback = $answer->get_feedback();
			if(!empty($feedback)){
				$text = $this->translate_text($feedback,$question);
				$item->add_modalFeedback(Qti::FEEDBACK, $id, 'show')->add_flow($text);
			}
		}
	}
	
	protected function add_interaction($body, $question){
		$result = $body->add_extendedTextInteraction(Qti::RESPONSE, '', 1, 1);
		return $result;
	}
    

}









