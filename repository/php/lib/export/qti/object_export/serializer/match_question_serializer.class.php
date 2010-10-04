<?php

/**
 * Serializer for match questions. 
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class MatchQuestionSerializer extends QuestionSerializer{
	
	static function factory($question, $target_root, $directory, $manifest, $toc){
		if($question instanceof MatchQuestion){
			return new self($target_root, $manifest, $directory, $manifest, $toc);
		}else{
			return null;
		}
	}
	
	protected function has_answer_feedback($question){
		return true;
	}
	
    protected function get_question_score($question){
    	$result = 0;
        $answers = $question->get_options();
        foreach($answers as $answer){
        	$result = max($result, $answer->get_weight());
        }
    	return $result;
    }

	protected function add_response_declaration(ImsQtiWriter $item, $question){
		$id = Qti::RESPONSE;
		$cardinality =  Qti::CARDINALITY_SINGLE;
		$type = Qti::BASETYPE_STRING;
    	$maximum_score = $this->get_question_score($question);
		$result = $item->add_responseDeclaration($id, $cardinality, $type);
		$correct = $result->add_correctResponse();
    	$mapping = $result->add_mapping(0, $maximum_score, 0);
	
        $answers = $question->get_options();
        foreach($answers as $index => $answer){
        	$value = $answer->get_value();
        	$score = $answer->get_weight();
        	if($score == $maximum_score){
        		$correct->add_value($value);
        	}
        	$mapping->add_mapEntry($value, $score);
        }
		return $result;
	}
	
	protected function add_score_processing($response_processing, $question){
    	return $response_processing->add_standard_response_map_response();
	}
	
	protected function add_answer_feedback(ImsQtiWriter $item, $question){   
		$count = 0;
        $answers = $question->get_options();
		foreach($answers as $answer){
    		$id = 'ID_' . ++$count;
    		$feedback = $answer->get_comment();
			if(!empty($feedback)){
				$text = $this->translate_text($feedback, $question);
				$item->add_modalFeedback(ImsQtiWriter::FEEDBACK, $id, 'show')->add_flow($text);
			}
		}
	}
	
	protected function add_answer_feedback_processing($processing, $question){
		$condition = $processing->add_responseCondition();
		$if = $condition->add_responseIf();
		$if->add_isNull()->add_variable(Qti::RESPONSE);
			$if->add_setOutcomeValue(Qti::FEEDBACK)->add_baseValue(Qti::BASETYPE_IDENTIFIER, 'DEFAULT_FEEDBACK');
			  
		$count = 0;
		$answers = $question->get_options();
        foreach($answers as $answer){
		    $if = $condition->add_responseElseIf();
		    $match = $if->add_match();
		      	$match->add_variable(Qti::RESPONSE);
		      	$match->add_baseValue(Qti::BASETYPE_STRING, $answer->get_value());
		    	$if->add_setOutcomeValue(Qti::FEEDBACK)->add_baseValue(Qti::BASETYPE_IDENTIFIER, 'ID_' . ++$count);
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