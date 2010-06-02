<?php

/**
 * Serializer for match questions. 
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class OrderingQuestionSerializer extends QuestionSerializer{
	
	static function factory($question, $target_root, $manifest){
		if($question instanceof OrderingQuestion){
			return new self($target_root, $manifest);
		}else{
			return null;
		}
	}
	
	protected function has_answer_feedback($question){
		return false;
	}
	
	protected function add_response_declaration(ImsQtiWriter $item, OrderingQuestion $question){
		$id = Qti::RESPONSE;
		$cardinality =  Qti::CARDINALITY_ORDERED;
		$type = Qti::BASETYPE_IDENTIFIER;
		$result = $item->add_responseDeclaration($id, $cardinality, $type);
		$correct = $result->add_correctResponse();
	
        $answers = $question->get_options();
        $scores = array();
        foreach($answers as $index => $answer){
        	$scores[$answer->get_order()] = "ID_$index";
        }
        ksort($scores, SORT_NUMERIC);
        foreach($scores as $choide_id){
        	$correct->add_value($choide_id);
        }
		return $result;
	}
	
	protected function add_response_processing(ImsQtiWriter $item, $question){
		return $item->add_responseProcessing('http://www.imsglobal.org/question/qti_v2p0/rptemplates/match_correct');
	}
	
	protected function add_interaction(ImsQtiWriter $body, $question){
		$result = $body->add_orderInteraction(Qti::RESPONSE, true);
        $answers = $question->get_options();
        foreach($answers as $index=>$answer){
        	$text = $this->translate_text($answer->get_value());
        	$result->add_simpleChoice("ID_$index")->add_flow($text);
        }
		return $result;
	}

}









