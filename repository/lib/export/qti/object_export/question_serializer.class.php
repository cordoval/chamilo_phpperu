<?php

/**
 * Base class for question serializers. 
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class QuestionSerializer extends SerializerBase{
	
	const GENERAL_FEEDBACK = 'GENERAL_FEEDBACK';
	const PENALTY = 'PENALTY';
		
	public function serialize($question){
		$writer = new ImsQtiWriter();
		$item = $this->add_assessment_item($writer, $question);
		$this->add_response_declaration($item, $question);
		$this->add_outcome_declaration($item, $question);
		$this->add_template_declaration($item, $question);
		$this->add_template_processing($item, $question);
		$this->add_stylesheet($item, $question);
		$this->add_body($item, $question);
		$this->response_processing = $this->add_response_processing($item, $question);
		$this->add_modal_feedback($item, $question);
		return $writer->saveXML();
	}
	
	protected function add_assessment_item(ImsQtiWriter $writer, $question){
    	$id =  self::get_identifier($question);
    	$title = $question->get_title();
    	$adaptive = false;
    	$time_dependent = false;
    	$tool_name = Qti::get_tool_name();
    	$tool_version = Qti::get_tool_version();
    	$lang = Translation::get_instance()->get_language();
		$lang = AdminDataManager :: get_instance()->retrieve_language_from_english_name($lang);
    	$lang = empty($lang) ? 'en' : $lang->get_isocode();
    	$result = $writer->add_assessmentItem($id, $title, $time_dependent, '', $adaptive, $lang, $tool_name, $tool_version);
    	return $result;
	}
		
	protected function add_stylesheet(ImsQtiWriter $item, $question){
		 return null;
	}
	
	protected function add_response_declaration(ImsQtiWriter $item, $question){
		return $item->add_responseDeclaration(ImsQtiWriter::RESPONSE, ImsQtiWriter::CARDINALITY_SINGLE, ImsQtiWriter::BASETYPE_IDENTIFIER);
	}
	
	protected function add_outcome_declaration(ImsQtiWriter $item, $question){
		$this->add_score_declaration($item, $question);
		//$this->add_penalty_declaration($item, $question);
		$this->add_general_feedback_declaration($item, $question);
		$this->add_answer_feedback_declaration($item, $question);
	}	
	
	//TEMPLATE

	protected function add_template_processing(ImsQtiWriter $item, $question){
		return null;
	}

	protected function add_template_declaration(ImsQtiWriter $item, $question){
		return null;
	}
	
	//BODY 
	
	protected function add_body(ImsQtiWriter $item, $question){
		$result = $item->add_itemBody();
		$text = $this->translate_text($question->get_description(), $question);
		$result->add_flow($text);
		$this->interaction = $this->add_interaction($result, $question);
		return $result;
	}
	
	protected function add_interaction(ImsQtiWriter $body, $question){
		return null;
	}

	//FEEDBACK
	
	protected function add_modal_feedback(ImsQtiWriter $item, $question){
		$this->add_general_feedback($item, $question);
		$this->add_answer_feedback($item, $question);
	}
	
	protected function has_general_feedback(ImsQtiWriter $item, $question){
		return false;
	}
	
	protected function add_general_feedback_declaration(ImsQtiWriter $item, $question){
		if($this->has_general_feedback($item, $question)){
			$id = self::GENERAL_FEEDBACK;
			$value = 'true';
			$result = $item->add_outcomeDeclaration_feedback($id)->add_defaultValue()->add_value($value);
			return $result;
		}else{
			return null;
		}
	}

	protected function add_general_feedback(ImsQtiWriter $item, $question){
		if($has_feedback = !empty($question->generalfeedback)){
			$id = self::GENERAL_FEEDBACK;
			$value = 'true';
			$text = $this->translate_text($question->generalfeedback, self::FORMAT_HTML, $question);
			$result = $item->add_modalFeedback($id, $value, 'show')->add_flow($text);
			return $result;
		}else{
			return null;
		}
	}
	
	protected function add_answer_feedback_declaration(ImsQtiWriter $item, $question){
		if($this->has_answer_feedback($question)){
			$result = $item->add_outcomeDeclaration_feedback();
			$result->add_defaultValue()->add_value('DEFAULT_FEEDBACK');
			return $result;
		}else{
			return null;
		}
	}
	
	protected function add_answer_feedback(ImsQtiWriter $item, $question){
		return null;
	}

	protected function add_answer_feedback_processing(ImsQtiWriter $processing, $question){
		if($this->has_answer_feedback($question)){
			$result = $processing->add_standard_response_assign_feedback();
			return $result;
		}else{
			return null;
		}
	}

	protected function has_answer_feedback($question){
		throw new Exception('not implemented');
	}
	
	// SCORE 
	
    protected function get_question_score($question){
    	$f = array($question, 'get_weight');
    	if(is_callable($f)){
    		$result = call_user_func($f);
    	}else{
    		$result = 1;
    	}
    	return $result;
    }
	
	protected function add_score_declaration(ImsQtiWriter $item, $question){
		$score = $this->get_question_score($question);
		$cardinality = ImsQtiWriter::CARDINALITY_SINGLE;
		$name = ImsQtiWriter::SCORE;
		$base_type = ImsQtiWriter::BASETYPE_FLOAT;
		$result = $item->add_outcomeDeclaration($name, $cardinality, $base_type, $score);
		$result->add_defaultValue()->add_value(0);
		return $result;
	}

	protected function add_score_processing($response_processing, $question){
		return null;	
	}
	
	// PENALTY
	
	protected function has_penalty($question){
		return !empty($question->penalty);
	}
	
	protected function add_penalty_declaration(ImsQtiWriter $item, $question){
		if(!$this->has_penalty($question)){
			return null;
		}
		
		$cardinality = ImsQtiWriter::CARDINALITY_SINGLE;
		$name = self::PENALTY;
		$base_type = ImsQtiWriter::BASETYPE_FLOAT ;
		$score_outcome = $item->add_outcomeDeclaration($name, $cardinality, $base_type);
		$score_outcome->add_defaultValue()->add_value(0);
		
		return $score_outcome;
	}
	
	protected function add_penalty_increase(ImsQtiWriter $processing, $question, $penalty_id = self::PENALTY){
		if(!$this->has_penalty($question)){
			return null;
		}
		$penalty_value = $question->penalty;
		$result = $processing->add_responseCondition();
    	$if = $result->add_responseIf();
    	$if->add_isNull()->add_variable($penalty_id);
    	$if->add_setOutcomeValue($penalty_id)->add_baseValue(ImsQtiWriter::BASETYPE_FLOAT, $penalty_value);
    	$sum = $result->add_responseElse()->add_setOutcomeValue($penalty_id)->add_sum();
    	$sum->add_baseValue(ImsQtiWriter::BASETYPE_FLOAT, $penalty_value);
    	$sum->add_variable($penalty_id);
    	return $result;
	}
	
	protected function add_add_penalty(ImsQtiWriter $processing, $question, $input_id = ImsQtiWriter::SCORE,  $score_id = ImsQtiWriter::SCORE, $penalty_id = self::PENALTY){
		if(!$this->has_penalty($question)){
			return null;
		}
		
		$result = $processing->add_setOutcomeValue($score_id);
    	$sum = $result->add_subtract();
    	$sum->add_variable($input_id);
    	$sum->add_variable($penalty_id);
    	
		$result = $processing->add_responseCondition();
    	$if = $result->add_responseIf();
    	$lt = $if->add_lt();
    	$lt->add_variable($input_id);
    	$lt->add_baseValue(ImsQtiWriter::BASETYPE_FLOAT, 0);    	
    	$if->add_setOutcomeValue($input_id)->add_baseValue(ImsQtiWriter::BASETYPE_FLOAT, 0);
    	return $result;
	}
	
	protected function add_response_processing(ImsQtiWriter $item, $question){
		$result = $item->add_responseProcessing();
		$this->add_score_processing($result, $question);
		//$this->add_add_penalty($result, $question);
		//$this->add_penalty_increase($result, $question); //last processing to perform;
		$this->add_answer_feedback_processing($result, $question);
		return $result;
	}
	
	//helper function
	
    protected function get_id(ContentObject $co){
		$id = $object->get_id();
		$id = str_pad($id, 8, '0', STR_PAD_LEFT);
    	return "ID_$id";
    }

}





