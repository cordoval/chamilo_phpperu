<?php

//@todo: review logic when question is working

/**
 * Serializer for fill in the blanks questions. 
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class FillInBlanksQuestionSerializer extends QuestionSerializer{
	
	const CLASS_DESCRIPTION = 'description';
	
	static function factory($question, $target_root, $directory, $manifest, $toc){
		if($question instanceof FillInBlanksQuestion){
			return new self($target_root, $directory, $manifest, $toc);
		}else{
			return null;
		}
	}
	
	protected function has_answer_feedback($question){
		return false;
	}
	
    protected function get_question_score($question, $index = -1){
    	if($index<0){
    		$result = 0;
        	$count = $question->get_number_of_questions();
        	for($index = 0; $index<$count; $index++){
        		$result += $this->get_question_score($question, $index);
        	}
    	}else{
    		$answers = $question->get_answers($index);
			$answer = $this->max_answer($answers);
			$result = $answer->get_weight();
    		
    	}
        return $result;
    }

	protected function add_response_declaration(ImsQtiWriter $item, FillInBlanksQuestion  $question){
		$result = null;
        $count = $question->get_number_of_questions();
        for($index = 0; $index<$count; $index++){
			$result = $item->add_responseDeclaration("RESPONSE_$index", Qti::CARDINALITY_SINGLE, Qti::BASETYPE_STRING);
			$answers = $question->get_answers($index);
			$answer = $this->max_answer($answers);
        	$value = trim($answer->get_value(), '[]');
			$correct = $result->add_correctResponse()->add_value($value);
			$mapping = $result->add_mapping(0, $answer->get_weight(), 0);
			foreach($answers as $answer_index => $answer){
				if($question->get_question_type() == FillInBlanksQuestion::TYPE_SELECT){
					$key = 'CHOICE_'.$index.'_'.$answer_index;
				}else if($question->get_question_type() == FillInBlanksQuestion::TYPE_TEXT){
					$key = $answer->get_value();
				}else{
					throw new Exception('Unknown question type '. $question->get_question_type());
				}
				$mapping->add_mapEntry($key, $answer->get_weight());
			}
			
			$feedback = $item->add_responseDeclaration("FEEDBACK_$index", Qti::CARDINALITY_SINGLE, Qti::BASETYPE_IDENTIFIER);
			$item->add_outcomeDeclaration("SCORE_$index", Qti::CARDINALITY_SINGLE, Qti::BASETYPE_FLOAT);
        }
		return $result;
	}
	
	protected function max_answer($answers){
		$result = null;
		foreach($answers as $answer){
			if(empty($result)){
				$result = $answer;
			}else if($result->get_weight() < $answer->get_weight()){
				$result = $answer;
			}
		}
		return $result;
	}
	
	protected function add_body(ImsQtiWriter $item, FillInBlanksQuestion $question){
		$result = $item->add_itemBody();
		//$description = $this->translate_text($question->get_description(), $question);
		//$result->add_rubricBlock(Qti::VIEW_ALL, self::CLASS_DESCRIPTION)->add_flow($description);
		$text = $question->get_answer_text();
		$parts = preg_split(FillInBlanksQuestionAnswer::CLOZE_REGEX, $text);
        $count = $question->get_number_of_questions();
		for($index = 0; $index<$count; $index++){
			$head = array_shift($parts);
			$result->add_flow($head);
        	$answers = $question->get_answers($index);
        	if($question->get_question_type() == FillInBlanksQuestion::TYPE_TEXT){
	        	$text_interaction = $result->add_textEntryInteraction("RESPONSE_$index");
	        	foreach($answers as $answer_index => $answer){
					$feedback_text = $this->translate_text($answer->get_comment());
					$result->add_feedbackInline("FEEDBACK_$index", 'CHOICE_'.$index.'_'.$answer_index, Qti::FEEDBACK_SHOW)->add_flow($feedback_text);	
	        	}
        	}else if($question->get_question_type() == FillInBlanksQuestion::TYPE_SELECT){
	        	$interaction = $result->add_inlineChoiceInteraction("RESPONSE_$index", true);
	        	foreach($answers as $answer_index => $answer){
	        		$answer_text = $answer->get_value();
	        		$interaction->add_inlineChoice('CHOICE_'.$index.'_'.$answer_index)->add_flow($answer_text);	
					$feedback_text = $this->translate_text($answer->get_comment());
					$result->add_feedbackInline("FEEDBACK_$index", 'CHOICE_'.$index.'_'.$answer_index, Qti::FEEDBACK_SHOW)->add_flow($feedback_text);	
	        	}
        	}else{
        		throw new Exception('Unknown type : ' . $question->get_question_type());
        	}
			
		}
		return $result;
	}
	
	protected function add_score_processing(ImsQtiWriter $response_processing, $question){
        $count = $question->get_number_of_questions();
        for($index = 0; $index<$count; $index++){
        	$response_processing->add_standard_response_map_response("RESPONSE_$index", "SCORE_$index");
        }
			
        $sum = $response_processing->add_setOutcomeValue(Qti::SCORE)->add_sum();
        for($index = 0; $index<$count; $index++){
        	$sum->add_variable("SCORE_$index");
        }
        
        for($index = 0; $index<$count; $index++){
        	if($question->get_question_type() == FillInBlanksQuestion::TYPE_SELECT){
        		$response_processing->add_standard_response_assign_feedback("RESPONSE_$index", "FEEDBACK_$index");
        	}else if($question->get_question_type() == FillInBlanksQuestion::TYPE_TEXT){
		 		$condition = $response_processing->add_responseCondition();
				$if = $condition->add_responseIf();
				$if->add_isNull()->add_variable("RESPONSE_$index");
					$if->add_setOutcomeValue("FEEDBACK_$index")->add_baseValue(Qti::BASETYPE_IDENTIFIER, 'DEFAULT_FEEDBACK');
				$answers = $question->get_answers($index);
		        foreach($answers as $answer_index => $answer){
				    $if = $condition->add_responseElseIf();
				    $match = $if->add_match();
				      	$match->add_variable("RESPONSE_$index");
				      	$match->add_baseValue(Qti::BASETYPE_STRING, $answer->get_value());
				    	$if->add_setOutcomeValue("FEEDBACK_$index")->add_baseValue(Qti::BASETYPE_IDENTIFIER, 'CHOICE_'.$index.'_'.$answer_index);
		        }
        	}else{
        		throw new Exception('Unknown type : ' . $question->get_question_type());
        	}
        }
	}
}









?>