<?php
/**
class QtiImportStrategy extends QtiImportStrategyBase{
	
	const DEFAULT_MAXIMUM_SCORE = 1;
	const DEFAULT_SCORE = 0;
		
	public static function get_main_interaction(ImsXmlReader $item){
		$interactions = $item->list_interactions();
		if(empty($interactions)){
			return $item->get_default_result();
		}else if(count($interactions)==1){
			return reset($interactions);
		}else{
			foreach($interactions as $interaction){
				if($interaction->responseIdentifier == Qti::RESPONSE){
					return $interaction;
				}
			}
			foreach($interactions as $interaction){
				$base_type = $item->get_by_id($interaction->responseIdentifier)->baseType;
				if($base_type == Qti::BASETYPE_FLOAT){
					return $interaction;
				}
			}
			foreach($interactions as $interaction){
				$base_type = $item->get_by_id($interaction->responseIdentifier)->baseType;
				if($base_type == Qti::BASETYPE_INTEGER){
					return $interaction;
				}
			}
		}
		
		return $item->get_default_result();
	}

	public static function is_numeric_interaction($item, $interaction){
		if(!$interaction->is_sliderInteraction() && !$interaction->is_textEntryInteraction()){
			return false;
		}
		$declaration = $item->get_by_id($interaction->responseIdentifier); 
		$base_type = $declaration->baseType;
		if($base_type != Qti::BASETYPE_FLOAT && $base_type != Qti::BASETYPE_INTEGER){
			return false;
		}
		
		if($declaration->cardinality != Qti::CARDINALITY_SINGLE){
			return false;
		}
		return true;
	}
	
	
	private $renderer = null;
	
	public function __construct(QtiRendererBase $renderer){
		$this->renderer = $renderer;
	}

	//RENDERER
		
	public function get_renderer(){
		return $this->renderer;
	}
	
	public function render($item){
		if(is_array($item)){
			$result = array();
			foreach($item as $el){
				$result[] = $this->to_html($el);
			}
			return $result;
		}else{
			return $this->to_html($el);
		}
	}
	
	public function to_html(ImsXmlReader $item){
		if(is_string($item)){
			return $item;
		}
		return $this->renderer->to_html($item);
	}
	
	public function to_text(ImsXmlReader $item){
		if(is_string($item)){
			return $item;
		}
		return $this->renderer->to_text($item);
	}
	
	//END RENDERER
	
	public function get_question_text(ImsXmlReader $item){
		$body = $item->get_itemBody(); 
		$result = $this->to_html($body);
		return $result;
	}
	
	public function get_question_title(ImsXmlReader $item){
		return $item->title;
	}
	
	//FEEDBACKS 
	
	public function get_feedbacks(ImsQtiReader $item, ImsQtiReader $interaction, $answer, $filter_out = array()){
		$modal_feedbacks = $this->get_modal_feedbacks($item, $interaction, $answer, $filter_out);
		$inline_feedbacks = $this->get_inline_feedbacks($item, $interaction, $answer, $filter_out);
		$result = array_merge($modal_feedbacks, $inline_feedbacks);
		$result = array_diff($result, $filter_out);
		return $result;
	}
	
	public function get_modal_feedbacks(ImsQtiReader $item, ImsQtiReader $interaction, $answer, $filter_out = array()){
		$interpreter = new QtiInterpreter();
		$interpreter->init($item);
		$interpreter->add_response($interaction, $answer);
		$interpreter->response($item);
		$this->renderer->init($interpreter);
		$result = $this->render($item->list_modalFeedback());
		$result = array_diff($result, $filter_out, array(''));
		return $result;
	}
	
	public function get_inline_feedbacks(ImsQtiReader $item, ImsQtiReader $interaction, $answer, $filter_out = array()){
		$interpreter = new QtiInterpreter();
		$interpreter->init($item);
		$interpreter->add_response($interaction, $answer);
		$interpreter->response($item);
		$this->renderer->init($interpreter);
		$result = array();
		$feedbacks = $item->query('.//def:feedbackInline');
		foreach($feedbacks as $feedback){
			$html = $this->to_html($feedback);
			if(!empty($html)){
				$result[] = $html;
			}
		}			
		$result = array_diff($result, $filter_out);
		return $result;
	}
	
	public function get_general_feedbacks(ImsQtiReader $item){
		$feedbacks = $item->list_modalFeedback();
		
		$interpreter = new QtiInterpreter();
		$interpreter->execute($item);
		$this->renderer->init($interpreter);
		$null_feedbacks = $this->render($feedbacks);
		
		$interpreter = new QtiInterpreter();
		$this->set_correct_responses($item, $interpreter);
		$interpreter->execute($item);
		$this->renderer->init($interpreter);
		$correct_feedbacks = $this->render($feedbacks);
	
		$interpreter = new QtiInterpreter();
		$this->set_incorrect_responses($item, $interpreter);
		$interpreter->execute($item);
		$this->renderer->init($interpreter);
		$incorrect_feedbacks = $this->render($feedbacks);
		
		$result = array();
		for($i = 0; $i<count($feedbacks); $i++){
			if(	$null_feedbacks[$i]==$correct_feedbacks[$i] 
				&& $correct_feedbacks[$i]==$incorrect_feedbacks[$i]
				&& !empty($correct_feedbacks[$i])){
				$result[]= $correct_feedbacks[$i];
			}
		}
		return $result;
	}
	
	public function get_correct_feedbacks(ImsQtiReader $item, $filter_out=array()){
		$feedback = $item->get_by_id('FEEDBACK_CORRECT');
		if($feedback->is_modalFeedback()){
			$this->renderer->reset_outcomes();
			$this->renderer->set_outcome($feedback->outcomeIdentifier, $feedback->identifier);
			$result =  array($this->renderer->to_html($feedback));
			$this->renderer->reset_outcomes();
			$result = array_diff($result, $filter_out, array(''));
			return $result;
		}else{
			$interpreter = new QtiInterpreter();
			$interpreter->init($item);
			$this->set_correct_responses($item, $interpreter);
			$interpreter->response($item);
			$this->renderer->init($interpreter);
			$result = $this->render($item->list_modalFeedback());
			$result = array_diff($result, $filter_out, array(''));
			return $result;
		}
	}

	public function get_partiallycorrect_feedbacks(ImsQtiReader $item, $filter_out=array()){
		$feedback = $item->get_by_id('FEEDBACK_PARTIALY_CORRECT');
		if($feedback->is_modalFeedback()){
			$this->renderer->set_outcome($feedback->outcomeIdentifier, $feedback->identifier);
			$result =  array($this->renderer->to_html($feedback));
			$this->renderer->reset_outcomes();
			$result = array_diff($result, $filter_out, array(''));
			return $result;
		}else{
			return array();
		}
	}
	
	public function get_incorrect_feedbacks(ImsQtiReader $item, $filter_out=array()){
		$feedback = $item->get_by_id('FEEDBACK_INCORRECT');
		if($feedback->is_modalFeedback()){
			$this->renderer->set_outcome($feedback->outcomeIdentifier, $feedback->identifier);
			$result =  array($this->renderer->to_html($feedback));
			$this->renderer->reset_outcomes();
			$result = array_diff($result, $filter_out, array(''));
			return $result;
		}else{
			$interpreter = new QtiInterpreter();
			$interpreter->execute($item);
			$this->renderer->init($interpreter);
			$result = $this->render($item->list_modalFeedback());
			$result = array_diff($result, $filter_out, array(''));
			return $result;
		}
	}
	
	//END FEEDBACKS
	
	//SCORE
	
	public function get_score(ImsQtiReader $item, ImsQtiReader $interaction, $answer, $outcome_id = ''){
		return $this->get_outcome($item, $interaction, $answer, $outcome_id);
	}
		
	public function get_outcome(ImsQtiReader $item, ImsQtiReader $interaction, $answer, $outcome_id = ''){
		$interpreter = new QtiInterpreter();
		$interpreter->init($item);
		
		if(empty($outcome_id)){
			$declaration = $this->get_score_outcome_declaration($item);
			if($declaration->is_empty()){ //i.e. no correct outcome
				return 0;
			}
			$outcome_id = $declaration->identifier;
		}
		$interpreter->add_response($interaction, $answer);
		$interpreter->response($item);
		$result = $interpreter->get_outcome($outcome_id);
		$result = empty($result) ? 0 : $result;
		return $result;
	}

	public function get_scores(ImsQtiReader $item, $responses, $interpreter = null){
		$result = array();
		$responses_id = array_keys($responses);
		if(empty($interpreter)){
			$interpreter = new QtiInterpreter();
			$interpreter->init($item);
		}
		if(count($interactions)==1){
			$answers = reset($responses);
			$response_id = reset($responses_id);
			foreach($answers as $answer){
				if($this->is_formula($answer)){ 
					$answer = $interpreter->execute($answer);
				}
				$interpreter->add_response($response_id, $answer);
				$score = $interpreter->get_outcome($score_id);
				$score = empty($score) ? 0 : $score;
				$result[$score] = $score;
			}
			return $result;
		}else if(count($interactions)==2){
			$answers_0 = $responses[0];
			$response_id_0 = $responses_id[0];
			$answers_1 = $responses[1];
			$response_id_1 = $responses_id[1];
			foreach($answers_0 as $answer_0){
				if($this->is_formula($answer_0)){ 
					$answer_0 = $interpreter->execute($answer_0);
				}
				$interpreter->add_response($response_id_0, $answer_0);
				foreach($answers_1 as $answer_1){
					if($this->is_formula($answer_1)){ 
						$answer_1 = $interpreter->execute($answer_1);
					}
					$interpreter->add_response($response_id_1, $answer_1);
					$score = $interpreter->get_outcome($score_id);
					$score = empty($score) ? 0 : $score;
					$result[$score] = $score;
				}
			}
			return $result;
			
		}else{
			throw new Exception('Not implemented, too many interactions: '.count($interactions));
		}
	}
	
	public function get_minimum_score(ImsQtiReader $item){
		$interpreter = new QtiInterpreter();
		$interpreter->execute($item);
		$score_id = $this->get_score_outcome_declaration($item)->identifier;
		$result = $interpreter->get_outcome($score_id);
		return empty($result) ? 0 : $result;	
	}
	
	public function get_maximum_score(ImsQtiReader $item){
		$score_declaration = $this->get_score_outcome_declaration($item);
		if($score_declaration->is_empty()){
			return 0;
		}
		if($normal_maximum = $score_declaration->normalMaximum){
			return $normal_maximum;
		}
		
		$responses = $this->get_maximum_score_possible_answers();
		$scores = $this->get_scores($item, $responses);
		return max($scores);
	}
	
	protected function get_maximum_score_possible_answers(ImsQtiReader $item){
		$result = array();
		foreach($interactions as $interaction){
			$answers = $this->get_correct_responses($item, $interaction);
			if(empty($answers)){
				$answers = $this->get_possible_responses($item, $interaction);
			}
			$cardinality = $this->get_response_declaration($item, $interaction)->cardinality;
			if($cardinality == qti::CARDINALITY_MULTIPLE){
    			$answers = $this->combine($answers);
			}
			$result[$interaction->responseIdentifier] = $answers;
		}
		return $result;
	}
	
	public function get_score_default(ImsXmlReader $item){
		$result = $this->get_score_outcome_declaration($item);
		$result = $result->get_defaultValue();
		$result = $result->first_value();
		$result = $result->value();
		$result = empty($result) ? 0 : round($result);
		return $result;
	}
	
	public function get_penalty(ImsQtiReader $item){
		$interpreter = new QtiInterpreter();
		$interpreter->execute($item);
		$result = $interpreter->get_outcome('PENALTY');
		return empty($result) ? 0 : $result;	
	}
	
	//END SCORE
	
	public function get_tolerance(ImsQtiReader $item, ImsQtiReader $interaction=null, $answer=''){
		if(!$interaction->is_sliderInteraction() && !$interaction->is_textEntryInteraction()){
			throw new Exception("Invalid interaction's type");
		}
		
		$interpreter = new QtiInterpreter();
		if($this->is_formula($answer)){ 
			$equal = $answer->get_parent();
			$if = $equal->get_parent();
			$set = $if->get_setOutcomeValue();
			$score_id = $this->get_score_outcome_declaration($item)->identifier;
			if($equal->is_equal() && $set->identifier == $score_id){
				$result = max(explode(' ', $equal->tolerance));
				$result = empty($result) ? 0 : $result;
				return $result;
			}else{
				$interpreter->init($item);
				$answer = $interpreter->execute($answer);
			}
		}else if(empty($answer)){
			$answer = $this->get_correct_responses($item, $interaction);
			$answer = reset($answer);
		}
		$outcome_base_type = $this->get_response_declaration($item, $interaction)->baseType;
		$answer_score = $this->get_score($item, $interaction, $answer, '', $interpreter);
		$lower_bound = $interaction->lowerBound;
		$upper_bound = $interaction->upperBound;
		$step = $interaction->step;
		$step = empty($step) ? ($upper_bound-$lower_bound) / 20 : $step;
		$step = $outcome_base_type == 'integer' ? round($step) : $step;
		$step = empty($step) ? 1 : $step;
		
		$value_top = $value_bottom = is_array($answer) ? reset($answer) : $answer;
		for($shift = $count = 0; $lower_bound<=$value_bottom && $value_top<=$upper_bound; $shift += $step, $count++){
			$value_top += $step;
			$value_bottom -= $step;
			$score_top = $this->get_score($item, $interaction, $value_top);
			$score_bottom = $this->get_score($item, $interaction, $value_bottom);
			if(abs($score_top-$answer_score)!=0  || abs($score_bottom-$answer_score)!=0 || $count > 50){
				break;
			}
		}
		$result = empty($shift) ? 0 : $shift;
		return $result;
	}
	
	public function get_partial_responses(ImsQtiReader $item, ImsQtiReader $interaction){
		if(!$interaction->is_sliderInteraction()){
			debug('Not implemented');
			return array();
		}
		$base_type = $this->get_response_declaration($item, $interaction)->baseType;
		$answers = $this->get_correct_responses($item, $interaction);
		$answer = reset($answers);
		$answer_score = $this->get_score($item, $interaction, $answer);
		$lower_bound = $interaction->lowerBound;
		$upper_bound = $interaction->upperBound;
		$step = $interaction->step;
		$step = empty($step) ? ($upper_bound-$lower_bound) / 20 : $step;
		$step = $base_type == 'integer' ? round($step) : $step;
		$step = empty($step) ? 1 : $step;
		$start = max($lower_bound, $answer - $step * 10);
		$stop = min($upper_bound, $answer + $step * 10);

		//$scores = array();
		$result = array();
		for($value = $start, $count = 0; $value<=$stop && $count<=50; $value += $step, $count++){
			$score = $this->get_score($item, $interaction, $value);
			if(abs($score-$answer_score)!=0 && $score != 0){
				$result[] = $value;
			}
		}
		return $result;
	}
	
	public function get_template_values(ImsQtiReader $item, $maximum = 100){
		$result = array();
		$parameters = $item->list_templateDeclaration();
		foreach($parameters as $param){
			$result[$param->identifier] = array();
		}
		$interpreter = new QtiInterpreter();
		for($i=0; $i<$maximum; $i++){
			$interpreter->reset();
			$interpreter->execute($item);
			foreach($parameters as $param){
				$id = $param->identifier;
				$value = $interpreter->get_template($id);
				$result[$id][$value] = $value;
			}
		}
		return $result;	
	}

	public function get_rubricBlock(ImsQtiReader $item, $role = QTI::VIEW_ALL){
		$result = array();
		$interpreter = new QtiInterpreter($role);
		$interpreter->execute($item);
		$this->renderer->init($interpreter);
		$rubrics = $item->query('.//def:rubricBlock');
		foreach($rubrics as $rubric){
			$html = $this->to_html($rubric);
			if(!empty($html)){
				$result[] = $html;
			}
		}			
		return $result;
	}
	
	public function list_outcome(ImsXmlReader $item, $include_feedback_outcome = false){
		$outcomes = $item->list_outcomeDeclaration();
		
		if($include_feedback_outcome){
			return $outcomes;
		}
		
		$inline_feedback = $item->query('.//feedbackInline');
		$modal_feedback = $item->query('.//modalFeedback');
		$feedbacks = array_merge($modal_feedback, $inline_feedback);
		
		$result = array();
		foreach($outcomes as $outcome){
			$id = $outcome->identifier;
			$is_feedback = false;
			foreach($feedbacks as $feedback){
				if($feedback->outcomeIdentifier == $id){
					$is_feedback = true;
					break;
				}
			}
			if(!$is_feedback){
				$result[] = $outcome;
			}
		}
		return $result;
	}
	
	public function get_score_outcome_declaration(ImsXmlReader $item){
		$score = $item->get_by_id('SCORE');
		if(!$score->is_empty()){
			return $score;
		}
		
		$outcomes = $item->list_outcomeDeclaration();
		$filtered = array();
		foreach($outcomes as $outcome){
			$type = strtolower($outcome->baseType); 
			if($type == 'float' || $type = 'integer'){
				$filtered[] = $outcome;
			}
		}
		foreach($filtered as $outcome){
			$type = strtolower($outcome->baseType); 
			if($type == 'float' ){
				return $outcome;
			}
		}
		foreach($filtered as $outcome){
			return $outcome;
		}
		return $item->get_default_result();
		
	}
	
	public function get_main_response(ImsXmlReader $item){
		return $item->get_by_id(Qti::RESPONSE);
	}
	
	public function get_response_declaration(ImsQtiReader $item, ImsQtiReader $interaction){
		return $item->get_child_by_id($interaction->responseIdentifier);
	}
	
	//FORMULA
	
	public function get_score_formulas(ImsQtiReader $item, ImsQtiReader $interaction){
		$result = array();
		$outcome_id = $this->get_response_declaration($item, $interaction)->identifier;
		$score_id = $this->get_score_outcome_declaration($item)->identifier;
		$conditions = $item->get_responseProcessing()->list_responseCondition();
		foreach($conditions as $condition){
			$formulas = $this->get_score_formula_from_condition($condition, $outcome_id, $score_id);
			$result = array_merge($result, $formulas);
		}
		return $result;
	}
	
	public function is_formula($item){
		return $item instanceof ImsQtiReader;
	}
		
	public function is_formula_constant(ImsQtiReader $item){
		return count($item->all_variable()) == 0; 
	}
	
	public function execute_formula(ImsQtiReader $item){
		$interpreter = new QtiInterpreter();
		$result = $interpreter->execute($item);
		return $result;
	}
	
	protected function get_score_formula_from_condition(ImsQtiReader $condition, $outcome_id, $score_id){
		$result = array();
		$ifs = array_merge($condition->list_responseIf(), $condition->list_responseElseIf());
		foreach($ifs as $if){
			if($formula = $this->get_score_formula_from_if($if, $outcome_id, $score_id)){
				$result[] = $formula;
			}
		}
		return $result;
	}
	
	protected function get_score_formula_from_if(ImsQtiReader $if, $response_id, $score_id){
		if(!$if->is_responseIf() && !$if->is_responseElseIf()){
			return false;
		}
		if($if->get_setOutcomeValue()->identifier != $score_id){
			return false;
		}
		$response_pattern = './/def:variable[@identifier="'.$response_id.'"]';
		$branches = $if->children();
		$condition = $branches[0];
		$response_rule = $branches[1];
		if(!$condition->exist($response_pattern) || $response_rule->exist($response_pattern)){
			return false;
		}
		
		if($condition->is_equal()){
			$branches = $condition->children();
			$has_response[0] = $branches[0]->exist($response_pattern);
			$has_response[1] = $branches[1]->exist($response_pattern);
			if(!$has_response[0] && $has_response[1]){
				return $branches[0];
			}else if($has_response[0] && !$has_response[1]){
				return $branches[1];
			}else{
				return false;
			}
		}else if($condition->is_patternMatch() && $condition->get_patternMatch()->get_variable()->identifier == $response_id){
			return $condition;
		}
		return false;
	}
	
	//END FORMULAS
	
	//CORRECT-INCORRECT METHODS
	
	public function get_correct_responses(ImsQtiReader $item, ImsQtiReader $interaction){
		$response = $this->get_response_declaration($item, $interaction);
		$correct_values = $response->get_correctResponse()->list_value();
		$result = array();
		foreach($correct_values as $correct_value){
			$value = $correct_value->valueof();
			$result[$value] = $value;
		}
		if(empty($result) && $interaction->is_choiceInteraction()){
			$choices = $interaction->list_simpleChoice();
			$answers = array();
			foreach($choices as $choice){
				$answer = $choice->identifier;
				$score = $this->get_score($item, $interaction, $answer);
				$answers[$answer] = $score;
			} 
			arsort($answers, SORT_NUMERIC);
			$max_choices = $interaction->maxChoices;
			$max_choices = empty($max_choices) ? 100000 : $max_choices;
			if($max_choices == 1){
				$result[] = reset($answers);
			}else{
				$response = array();
				$count = min($max_choices, count($answers));
				$i=0;
				foreach($answers as $answer=>$score){
					if($i<$count){
						$response[] = $answer;
						$i++;
					}else{
						break;
					}
				}
				$result = $response;
			}
		}
		return $result;
	}
	
	public function get_possible_responses(ImsQtiReader $item, ImsQtiReader $interaction){
		$result = array();
		if($interaction->is_choiceInteraction()){
			$choices = $interaction->list_simpleChoice();
			foreach($choices as $choice){
				$result[] = $choice->identifier;
			}
		}
		if(empty($result)){
			$response = $this->get_response_declaration($item, $interaction);
			$entries = $response->get_mapping()->list_mapEntry();
			foreach($entries as $entry){
				$result[] = $entry->mapKey;
			}
		}
		if(empty($result) && $interaction->is_sliderInteraction()){
			$base_type = $this->get_response_declaration($item, $interaction)->baseType;
			$answers = $this->get_correct_responses($item, $interaction);
			$answer = reset($answers);
			$answer_score = $this->get_score($item, $interaction, $answer);
			$lower_bound = $interaction->lowerBound;
			$upper_bound = $interaction->upperBound;
			$step = $interaction->step;
			$step = empty($step) ? ($upper_bound-$lower_bound) / 20 : $step;
			$step = $base_type == 'integer' ? round($step) : $step;
			$step = empty($step) ? 1 : $step;
			$start = max($lower_bound, $answer - $step * 10);
			$stop = min($upper_bound, $answer + $step * 10);
	
			//$scores = array();
			$result = array();
			for($value = $start, $count = 0; $value<=$stop && $count<=50; $value += $step, $count++){
				$score = $this->get_score($item, $interaction, $value);
				if(abs($score-$answer_score)!=0 && $score != 0){
					$result[] = $value;
				}
			}
		}
		if(empty($result)){
			$result = $this->get_score_formulas($item, $interaction);
		}
		return $result;
	}

	public function get_possible_responses_text(ImsQtiReader $item, ImsQtiReader $interaction){
		$result = array();
		if($interaction->is_choiceInteraction()){
			$choices = $interaction->list_simpleChoice();
			foreach($choices as $choice){
				$result[] = $choice->value();
			}
		}else{
			$response = $this->get_response_declaration($item, $interaction);
			$entries = $response->get_mapping()->get_mapEntry();
			foreach($entries as $entry){
				$result[] = $entry->mapKey;
			}
		}
		return $result;
	}

	protected function set_correct_responses(ImsQtiReader $item, $interpreter){
		$interactions = $item->list_interactions();
		foreach($interactions as $interaction){
			$correct_responses = $this->get_correct_responses($item, $interaction);				
			$id = $interaction->responseIdentifier;
			$cardinality = $this->get_response_declaration($item, $interaction)->cardinality;
			$correct_responses = $cardinality == Qti::CARDINALITY_MULTIPLE ? $correct_responses : reset($correct_responses);
			$interpreter->add_response($id, $correct_responses);
		}
	}
	
	protected function set_incorrect_responses(ImsQtiReader $item, $interpreter){
		$interactions = $item->list_interactions();
		foreach($interactions as $interaction){
			$incorrect_response = $this->get_incorrect_response($item, $interaction);				
			$id = $interaction->responseIdentifier;
			$interpreter->add_response($id, $incorrect_response);
		}
	}
	
	protected function get_incorrect_response(ImsQtiReader $item, $interaction){
		$correct_responses = $this->get_correct_responses($item, $interaction);
		$base_type = $this->get_response_declaration($item, $interaction)->baseType;
		
		$count = 0;
		$answer = $this->random_value($base_type);
		while($this->get_score($item, $interaction, $answer) != 0 && $count<50){
			$answer = $this->random_value($base_type, ++$count);
		}
		return $count<50 ? $answer : null;
	}
	
	protected function random_value($base_type, $seed=0){
		switch($base_type){
			case Qti::BASETYPE_BOOLEAN:
				return $seed % 2 == 0 ? true : false;
			case Qti::BASETYPE_DIRECTEDPAIR:
			case Qti::BASETYPE_PAIR:
			case Qti::BASETYPE_POINT:
				return array(15000.5 * $seed, 15000.5 * $seed+1);
			case Qti::BASETYPE_DURATION:
				return $seed;
			case Qti::BASETYPE_FILE:
			case Qti::BASETYPE_IDENTIFIER:
			case Qti::BASETYPE_STRING:
			case Qti::BASETYPE_URI:
				return 'aasdfervrroinomaoino' . $seed;
			case Qti::BASETYPE_FLOAT:
				return 15000.5 * $seed;
			case Qti::BASETYPE_INTEGER:
				return 15000 * $seed;
			default:
				debug('Unknown base type: ' . $base_type);
		}
	}

	protected function combine($values, $max = 50){
		$result = array();
		$arrays = array();
		$count = 0;
		for($i=0; $i<5; $i++){
			$arrays[] = $values;
			$result = array_merge($result, $this->combine_arrays($arrays, $max, &$count));
		}
		return $result;
	}
	
	protected function combine_arrays($arrays, $max, &$count){
		$result = array();
		$args = func_get_args();
		if(count($args)==0){
			return $result;
		}else if(count($args)==1){
			foreach($args as $arg){
				if(++$count>$max){
					return $result;
				}else{
					$result[] = array($arg=>$arg);
				}
			}
			return $result;
		}else{
			$head = array_shift($args);
			$tail = $this->combine_arrays($args, $max, &$count);
			
			foreach($head as $h){
				foreach($tail as $t){
					if(++$count>$max){
						return $result;
					}else{
						$t[$h] = $h;
						$result[] = $t;
					}
				}
			}
		return $result;
		}
		
	}
	
	//END CORRECT-INCORRECT METHODS

}

*/
















