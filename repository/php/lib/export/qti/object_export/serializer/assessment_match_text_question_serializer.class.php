<?php

/**
 * Serializer for Assessment Match Text Questions. 
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class AssessmentMatchTextQuestionSerializer extends QuestionSerializer{
	
	static function factory($question, $target_root, $directory, $manifest, $toc){
		if($question instanceof AssessmentMatchTextQuestion){
			return new self($target_root, $directory, $manifest, $toc);
		}else{
			return null;
		}
	}
	
	protected function has_answer_feedback($question){
		return true;
	}
	
    protected function get_question_score(AssessmentMatchTextQuestion $question){
    	$result = 0;
        $answers = $question->get_options();
        foreach($answers as $answer){
        	$result = max($result, $answer->get_score());
        }
    	return $result;
    }

	protected function add_response_declaration(ImsQtiWriter $item, AssessmentMatchTextQuestion $question){
		$id = Qti::RESPONSE;
		$cardinality =  Qti::CARDINALITY_SINGLE;
		$type = Qti::BASETYPE_STRING;
		$result = $item->add_responseDeclaration($id, $cardinality, $type);
		
		if(!$question->get_use_wildcards()){
    		$maximum_score = $this->get_question_score($question);
			$correct = $result->add_correctResponse();
			
        	$answers = $question->get_options();
	        foreach($answers as $index => $answer){
	        	$value = $answer->get_value();
	        	$score = $answer->get_score();
	        	if($score == $maximum_score){
	        		$correct->add_value($value);
	        	}
	        }
		}
		return $result;
	}
	
	protected function add_score_processing(ImsQtiWriter $response_processing, AssessmentMatchTextQuestion $question){
		$result = $response_processing->add_responseCondition();
		$response_id = Qti::RESPONSE;
		$outcome_id = Qti::SCORE;
    	$if = $result->add_responseIf();
    	$if->add_isNull()->add_variable($response_id);
    	$if->add_setOutcomeValue($outcome_id)->add_baseValue(Qti::BASETYPE_FLOAT, 0);
        	
    	$answers = $question->get_options();
    	foreach($answers as $answer){
    		$score = $answer->get_score();
    		$elseif = $result->add_responseElseIf();
    		if($question->get_use_wildcards() && Wildcard::has_wildcard($answer->get_value())){
				$pattern = Wildcard::to_regex($answer->get_value(), !$question->get_ignore_case());
	    		$elseif->add_patternMatch($pattern)->add_variable($response_id);
    		}else{
	    		$match = $elseif->add_stringMatch(!$question->get_ignore_case());
	    		$match->add_variable($response_id);
	    		$match->add_baseValue(Qti::BASETYPE_STRING, $answer->get_value());
    		}
	    	$elseif->add_setOutcomeValue($outcome_id)->add_baseValue(Qti::BASETYPE_FLOAT, $score);
	 	}
    	$else = $result->add_responseElse();
    	$else->add_setOutcomeValue($outcome_id)->add_baseValue(Qti::BASETYPE_FLOAT, 0);
		return $result;
	}
	
	protected function add_answer_feedback_processing(ImsQtiWriter $response_processing, $question){
		$result = $response_processing->add_responseCondition();
		$response_id = Qti::RESPONSE;
		$outcome_id = Qti::FEEDBACK;
    	$if = $result->add_responseIf();
    	$if->add_isNull()->add_variable($response_id);
    	$if->add_setOutcomeValue($outcome_id)->add_baseValue(Qti::BASETYPE_IDENTIFIER, 'ID_0');
    	$count = 0;
    	$answers = $question->get_options();
    	foreach($answers as $answer){
    		$id = 'ID_' . ++$count;
    		$elseif = $result->add_responseElseIf();
    		if($question->get_use_wildcards() && Wildcard::has_wildcard($answer->get_value())){
				$pattern = Wildcard::to_regex($answer->get_value(), !$question->get_ignore_case());	
	    		$elseif->add_patternMatch($pattern)->add_variable($response_id);
    		}else{
	    		$match = $elseif->add_stringMatch(!$question->get_ignore_case());
	    		$match->add_variable($response_id);
	    		$match->add_baseValue(Qti::BASETYPE_STRING, $answer->get_value());
    		}
	    	$elseif->add_setOutcomeValue($outcome_id)->add_baseValue(Qti::BASETYPE_IDENTIFIER, $id);
	 	}
    	$else = $result->add_responseElse();
    	$else->add_setOutcomeValue($outcome_id)->add_baseValue(Qti::BASETYPE_IDENTIFIER, 'ID_0');
		return $result;
	}
	
	protected function add_answer_feedback(ImsQtiWriter $item, $question){   
		$count = 0;
        $answers = $question->get_options();
		foreach($answers as $answer){
    		$id = 'ID_' . ++$count;
    		$feedback = $answer->get_feedback();
			if(!empty($feedback)){
				$text = $this->translate_text($feedback, $question);
				$item->add_modalFeedback(Qti::FEEDBACK, $id, 'show')->add_flow($text);
			}
		}
	}
	
	protected function add_interaction(ImsQtiWriter $body, $question){
		$expectedLength = $this->get_answer_max_length($question);
		$result = $body->add_extendedTextInteraction(Qti::RESPONSE, $expectedLength, 1, 1);
		return $result;
	}
	
	protected function get_answer_max_length($question){
		$result = 0;
        $answers = $question->get_options();
        foreach($answers as $answer){
        	$result = max($result, strlen($answer->get_value()));
        }
        return $result;
	}

}









?>