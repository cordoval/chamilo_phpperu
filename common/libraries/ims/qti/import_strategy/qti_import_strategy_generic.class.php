<?php

/**
 * Generic import strategy. Make as little assumptions as possible about the qti file.
 *
 * University of Geneva
 * @author laurent.opprecht@unige.ch
 *
 */
class QtiImportStrategyGeneric extends QtiImportStrategyBase{

	public function __construct(QtiRendererBase $renderer, $head){
		parent::__construct($renderer, $head);
	}

	public function get_question_text(ImsXmlReader $item){
		$body = $item->get_itemBody();
		$result = $this->to_html($body);
		return $result;
	}

	public function get_question_title(ImsXmlReader $item){
		return $item->title;
	}

	//FEEDBACKS

	public function get_feedbacks(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $filter_out = array()){
		$modal_feedbacks = $this->head()->get_modal_feedbacks($item, $interaction, $answer, $filter_out);
		$inline_feedbacks = $this->head()->get_inline_feedbacks($item, $interaction, $answer, $filter_out);
		$result = array_merge($modal_feedbacks, $inline_feedbacks);
		$result = array_diff($result, $filter_out);
		return $result;
	}

	public function get_modal_feedbacks(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $filter_out = array()){
		$interpreter = new QtiInterpreter();
		$interpreter->init($item);
		$interpreter->add_response($interaction, $answer);
		$interpreter->response($item);
		$this->get_renderer()->init($interpreter);
		$result = $this->render($item->list_modalFeedback());
		$result = array_diff($result, $filter_out, array(''));
		$this->get_renderer()->reset_outcomes();
		return $result;
	}

	public function get_inline_feedbacks(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $filter_out = array()){
		$interpreter = new QtiInterpreter();
		$interpreter->init($item);
		$interpreter->add_response($interaction, $answer);
		$interpreter->response($item);
		$this->get_renderer()->init($interpreter);
		$result = $this->render($item->all_feedbackInline());
		$result = array_diff($result, $filter_out, array(''));
		$this->get_renderer()->reset_outcomes();
		return $result;
	}

	/**
	 * I.e. feedbacks that are always output
	 * @param ImsXmlReader $item
	 * @return array
	 */
	public function get_general_feedbacks(ImsXmlReader $item){
		$feedbacks = $item->list_modalFeedback();

		$interpreter = new QtiInterpreter();
		$interpreter->execute($item);
		$this->get_renderer()->init($interpreter);
		$null_feedbacks = $this->render($feedbacks);

		$interpreter = new QtiInterpreter();
		$this->head()->set_correct_responses($item, $interpreter);
		$interpreter->execute($item);
		$this->get_renderer()->init($interpreter);
		$correct_feedbacks = $this->render($feedbacks);

		$interpreter = new QtiInterpreter();
		$this->head()->set_incorrect_responses($item, $interpreter);
		$interpreter->execute($item);
		$this->get_renderer()->init($interpreter);
		$incorrect_feedbacks = $this->render($feedbacks);

		$result = array();
		for($i = 0; $i<count($feedbacks); $i++){
			if(	$null_feedbacks[$i]==$correct_feedbacks[$i]
				&& $correct_feedbacks[$i]==$incorrect_feedbacks[$i]
				&& !empty($correct_feedbacks[$i])){
				$result[]= $correct_feedbacks[$i];
			}
		}
		$this->get_renderer()->reset_outcomes();
		return $result;
	}

	/**
	 * Feedback for any correct response
	 * @param ImsXmlReader $item
	 * @param array $filter_out
	 * @return array
	 */
	public function get_correct_feedbacks(ImsXmlReader $item, $filter_out=array()){
		$interpreter = new QtiInterpreter();
		$interpreter->init($item);
		$this->head()->set_correct_responses($item, $interpreter);
		$interpreter->response($item);
		$this->get_renderer()->init($interpreter);
		$result = $this->render($item->list_modalFeedback());
		$result = array_diff($result, $filter_out, array(''));
		$this->get_renderer()->reset_outcomes();
		return $result;
	}

	/**
	 * Feedback for any partially correct response
	 * @param ImsXmlReader $item
	 * @param array $filter_out
	 * @return array
	 */
	public function get_partiallycorrect_feedbacks(ImsXmlReader $item, $filter_out=array()){
		return false;

	}

	/**
	 * Feedback for any incorrect response
	 * @param ImsXmlReader $item
	 * @param array $filter_out
	 * @return array
	 */
	public function get_incorrect_feedbacks(ImsXmlReader $item, $filter_out=array()){
		$interpreter = new QtiInterpreter();
		$interpreter->execute($item);
		$this->get_renderer()->init($interpreter);
		$result = $this->render($item->list_modalFeedback());
		$result = array_diff($result, $filter_out, array(''));
		$this->get_renderer()->reset_outcomes();
		return $result;
	}

	/**
	 * Returns all visible inline feedbacks which are a descendant of parent
	 * @param ImsXmlReader $item
	 * @param ImsXmlReader $interaction
	 * @param any $answer
	 * @param ImsXmlReader $parent
	 * @return array
	 */
	public function get_children_feedbacks(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $parent){
		$interpreter = new QtiInterpreter();
		$interpreter->init($item);
		$interpreter->add_response($interaction, $answer);
		$interpreter->response($item);
		$this->get_renderer()->init($interpreter);
		$result = $this->render($parent->all_feedbackInline());
		$this->get_renderer()->reset_outcomes();
		return $result;
	}

	//END FEEDBACKS

	//SCORE

	public function get_outcome(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $outcome_id = ''){
		$interpreter = new QtiInterpreter();
		$interpreter->init($item);

		if(empty($outcome_id)){
			$declaration = $this->head()->get_score_outcome_declaration($item);
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

	public function get_scores(ImsXmlReader $item, $responses, $interpreter = null){
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
				if($this->head()->is_formula($answer)){
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
				if($this->head()->is_formula($answer_0)){
					$answer_0 = $interpreter->execute($answer_0);
				}
				$interpreter->add_response($response_id_0, $answer_0);
				foreach($answers_1 as $answer_1){
					if($this->head()->is_formula($answer_1)){
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

	public function get_minimum_score(ImsXmlReader $item){
		$interpreter = new QtiInterpreter();
		$interpreter->execute($item);
		$score_id = $this->head()->get_score_outcome_declaration($item)->identifier;
		$result = $interpreter->get_outcome($score_id);
		return empty($result) ? 0 : $result;
	}

	public function get_maximum_score(ImsXmlReader $item, $interaction = null){
		$score_declaration = $this->head()->get_score_outcome_declaration($item);
		if($score_declaration->is_empty()){
			return 0;
		}

		if($normal_maximum = $score_declaration->normalMaximum){
			return $normal_maximum;
		}else if(empty($interaction)){
			$answers = $this->get_maximum_score_possible_answers($item);
			$scores = $this->get_scores($item, $answers);
			return max($scores);
		}else{
			$result = 0;
			$responses = $this->head()->get_possible_responses($item, $interaction);
			foreach($responses as $response){
				$score = $this->head()->get_score($item, $interaction, $response);
				$result = max($score, $result);
			}
			return $result;
		}
	}

	protected function get_maximum_score_possible_answers(ImsXmlReader $item){
		$result = array();
		foreach($interactions as $interaction){
			$answers = $this->head()->get_correct_responses($item, $interaction);
			if(empty($answers)){
				$answers = $this->head()->get_possible_responses($item, $interaction);
			}
			$cardinality = $this->head()->get_response_declaration($item, $interaction)->cardinality;
			if($cardinality == qti::CARDINALITY_MULTIPLE){
    			$answers = $this->combine($answers);
			}
			$result[$interaction->responseIdentifier] = $answers;
		}
		return $result;
	}

	public function get_score_default(ImsXmlReader $item){
		$result = $this->head()->get_score_outcome_declaration($item);
		$result = $result->get_defaultValue();
		$result = $result->first_value();
		$result = $result->value();
		$result = empty($result) ? 0 : round($result);
		return $result;
	}

	public function get_penalty(ImsXmlReader $item, $interaction = null, $response = null){
		if(empty($response)){
			$max_penalty = 0;
			$interaction = $this->head()->get_main_interaction($item);
			$responses = $this->head()->get_possible_responses($item, $interaction);
			foreach($responses as $response){
				$response_penalty = $this->get_penalty($item, $interaction, $response);
				$max_penatly = max($response_penalty, $max_penalty);
			}
			$result = $max_penalty;
		}else{
			$interpreter = new QtiInterpreter();
			$interpreter->init($item);
			$interpreter->add_response($interaction->responseIdentifier, $response);
			$interpreter->execute($item);
			$score_without_penalty = $interpreter->get_outcome(Qti::SCORE);

			$interpreter = new QtiInterpreter();
			$interpreter->init($item);
			$interpreter->execute($item);
			$interpreter->add_response($interaction->responseIdentifier, $response);
			$interpreter->execute($item);
			$score_with_penalty = $interpreter->get_outcome(Qti::SCORE);

			$result = abs($score_without_penalty - $score_with_penalty);
		}
		return empty($result) ? 0 : $result;
	}



	//END SCORE

	public function get_tolerance(ImsXmlReader $item, ImsXmlReader $interaction=null, $answer=''){
		if(	!$interaction->is_sliderInteraction() &&
			!$interaction->is_textEntryInteraction() &&
			!!$interaction->is_extendedTextEntryInteraction()){
			return false;
		}

		$interpreter = new QtiInterpreter();
		if($this->head()->is_formula($answer)){
			$equal = $answer->get_parent();
			$if = $equal->get_parent();
			$set = $if->get_setOutcomeValue();
			$score_id = $this->head()->get_score_outcome_declaration($item)->identifier;
			if($equal->is_equal() && $set->identifier == $score_id){
				$result = max(explode(' ', $equal->tolerance));
				$result = empty($result) ? 0 : $result;
				return $result;
			}else{
				$interpreter->init($item);
				$answer = $interpreter->execute($answer);
			}
		}else if(empty($answer)){
			$answer = $this->head()->get_correct_responses($item, $interaction);
			$answer = reset($answer);
		}
		$outcome_base_type = $this->head()->get_response_declaration($item, $interaction)->baseType;
		$answer_score = $this->head()->get_score($item, $interaction, $answer, '', $interpreter);
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
			$score_top = $this->head()->get_score($item, $interaction, $value_top);
			$score_bottom = $this->head()->get_score($item, $interaction, $value_bottom);
			if(abs($score_top-$answer_score)!=0  || abs($score_bottom-$answer_score)!=0 || $count > 50){
				break;
			}
		}
		$result = empty($shift) ? 0 : $shift;
		return $result;
	}

	public function get_tolerance_type(ImsXmlReader $item, ImsXmlReader $interaction=null, $answer=''){
		if(	!$interaction->is_sliderInteraction() &&
			!$interaction->is_textEntryInteraction() &&
			!$interaction->is_extendedTextInteraction()){
			return false;
		}
		if($this->head()->is_formula($answer)){
			$equal = $answer->get_parent();
			$if = $equal->get_parent();
			$set = $if->get_setOutcomeValue();
			$score_id = $this->head()->get_score_outcome_declaration($item)->identifier;
			if($equal->is_equal() && $set->identifier == $score_id){
				$result = $equal->toleranceMode;
				return $result;
			}
		}
		return false;
	}

	/**
	 *
	 * @param $item
	 * @param $interaction
	 */
	public function get_partial_responses(ImsXmlReader $item, ImsXmlReader $interaction){
		if(!$interaction->is_sliderInteraction()){
			debug('Not implemented');
			return array();
		}
		$base_type = $this->head()->get_response_declaration($item, $interaction)->baseType;
		$answers = $this->head()->get_correct_responses($item, $interaction);
		$answer = reset($answers);
		$answer_score = $this->head()->get_score($item, $interaction, $answer);
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
			$score = $this->head()->get_score($item, $interaction, $value);
			if(abs($score-$answer_score)!=0 && $score != 0){
				$result[] = $value;
			}
		}
		/*
		$result = array();
		$start = $stop = $current_score = false;
		foreach($scores as $value => $score){
			if($current_score === false || $current_score != $score){
				if($current_score !== false){
					$middle = ($start+$stop) / 2;
					$middle = $base_type == 'integer' ? round($middle) : $middle;
					$result[] = $middle;
				}
				$start = $stop = $value;
				$current_score = $core;
			}else{
				$start = min($value, $start);
				$end = max($value, $end);
			}
		}
		*/
		return $result;
	}

	public function get_template_values(ImsXmlReader $item, $maximum = 100){
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

	public function get_rubricBlock(ImsXmlReader $item, $role = QTI::VIEW_ALL){
		$result = array();
		$interpreter = new QtiInterpreter($role);
		$interpreter->execute($item);
		$this->get_renderer()->init($interpreter);
		$rubrics = $item->query('.//def:rubricBlock');
		foreach($rubrics as $rubric){
			$html = $this->to_html($rubric);
			if(!empty($html)){
				$result[] = $html;
			}
		}
		$this->get_renderer()->reset_outcomes();
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

	/**
	 *
	 * @param ImsXmlReader $item
	 * @return ImsQtiReader
	 */
	public function get_score_outcome_declaration(ImsXmlReader $item){
		$score = $item->get_by_id('SCORE');
		if(!$score->is_empty()){
			return $score;
		}

		$outcomes = $item->list_outcomeDeclaration();
		$filtered = array();
		foreach($outcomes as $outcome){
			$type = strtolower($outcome->baseType);
			if($type == 'float' || $type == 'integer'){
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

	public function get_response_declaration(ImsXmlReader $item, ImsXmlReader $interaction){
		return $item->get_child_by_id($interaction->responseIdentifier);
	}

	//FORMULA

	/**
	 * Returns expressions used to compare against the interaction's response for score processing.
	 * @param $item
	 * @param $interaction
	 */
	public function get_score_formulas(ImsXmlReader $item, ImsXmlReader $interaction){
		$result = array();
		$outcome_id = $this->head()->get_response_declaration($item, $interaction)->identifier;
		$score_id = $this->head()->get_score_outcome_declaration($item)->identifier;
		$conditions = $item->get_responseProcessing()->list_responseCondition();
		foreach($conditions as $condition){
			$formulas = $this->get_score_formula_from_condition($condition, $outcome_id, $score_id);
			$result = array_merge($result, $formulas);
		}
		return $result;
	}

	public function is_formula($item){
		return $item instanceof ImsXmlReader;
	}

	public function is_formula_constant(ImsXmlReader $item){
		return count($item->all_variable()) == 0;
	}

	public function execute_formula(ImsXmlReader $item, ImsXmlReader $formula){
		$interpreter = new QtiInterpreter();
		$interpreter->init($item);
		$result = $interpreter->execute($formula);
		return $result;
	}

	protected function get_score_formula_from_condition(ImsXmlReader $condition, $outcome_id, $score_id){
		$result = array();
		$ifs = array_merge($condition->list_responseIf(), $condition->list_responseElseIf());
		foreach($ifs as $if){
			if($formula = $this->get_score_formula_from_if($if, $outcome_id, $score_id)){
				$result[] = $formula;
			}
		}
		return $result;
	}

	protected function get_score_formula_from_if(ImsXmlReader $if, $response_id, $score_id){
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

	public function get_correct_responses(ImsXmlReader $item, ImsXmlReader $interaction){
		$response = $this->head()->get_response_declaration($item, $interaction);
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
				$score = $this->head()->get_score($item, $interaction, $answer);
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

	public function get_possible_responses(ImsXmlReader $item, ImsXmlReader $interaction){
		$result = array();
		if($interaction->is_choiceInteraction()){
			$choices = $interaction->list_simpleChoice();
			foreach($choices as $choice){
				$result[] = $choice->identifier;
			}
		}
		if($interaction->is_inlineChoiceInteraction()){
			$choices = $interaction->list_inlineChoice();
			foreach($choices as $choice){
				$result[] = $choice->identifier;
			}
		}
		if($interaction->is_hotspotInteraction()){
			$choices = $interaction->list_hotspotChoice();
			foreach($choices as $choice){
				$result[] = $choice->identifier;
			}
		}
		if(empty($result)){
			$response = $this->head()->get_response_declaration($item, $interaction);
			$entries = $response->get_areaMapping()->list_areaMapEntry();
			foreach($entries as $entry){
				$result[] = $entry;
			}
		}
		if(empty($result)){
			$response = $this->head()->get_response_declaration($item, $interaction);
			$entries = $response->get_mapping()->list_mapEntry();
			foreach($entries as $entry){
				$result[] = $entry->mapKey;
			}
		}
		if(empty($result) && $interaction->is_sliderInteraction()){
			$base_type = $this->head()->get_response_declaration($item, $interaction)->baseType;
			$answers = $this->head()->get_correct_responses($item, $interaction);
			$answer = reset($answers);
			$answer_score = $this->head()->get_score($item, $interaction, $answer);
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
				$score = $this->head()->get_score($item, $interaction, $value);
				if(abs($score-$answer_score)!=0 && $score != 0){
					$result[] = $value;
				}
			}
		}
		//if(empty($result) && $answers = $this->head()->get_correct_responses($item, $interaction)){
		//	$result = $answers;
		//}
		if(empty($result)){
			$result = $this->head()->get_score_formulas($item, $interaction);
		}
		return $result;
	}

	public function get_possible_responses_text(ImsXmlReader $item, ImsXmlReader $interaction){
		$result = array();
		if($interaction->is_choiceInteraction()){
			$choices = $interaction->list_simpleChoice();
			foreach($choices as $choice){
				$result[] = $choice->value();
			}
		}else{
			$response = $this->head()->get_response_declaration($item, $interaction);
			$entries = $response->get_mapping()->list_mapEntry();
			foreach($entries as $entry){
				$result[] = $entry->mapKey;
			}
		}
		return $result;
	}
/*
	protected function set_correct_responses(ImsXmlReader $item, $interpreter){
		$interactions = $item->list_interactions();
		foreach($interactions as $interaction){
			$correct_responses = $this->head()->get_correct_responses($item, $interaction);
			$id = $interaction->responseIdentifier;
			$cardinality = $this->head()->get_response_declaration($item, $interaction)->cardinality;
			$correct_responses = $cardinality == Qti::CARDINALITY_MULTIPLE ? $correct_responses : reset($correct_responses);
			$interpreter->add_response($id, $correct_responses);
		}
	}

	protected function set_incorrect_responses(ImsXmlReader $item, $interpreter){
		$interactions = $item->list_interactions();
		foreach($interactions as $interaction){
			$incorrect_response = $this->head()->get_incorrect_response($item, $interaction);
			$id = $interaction->responseIdentifier;
			$interpreter->add_response($id, $incorrect_response);
		}
	}
	*/

	protected function get_incorrect_response(ImsXmlReader $item, $interaction){
		$correct_responses = $this->head()->get_correct_responses($item, $interaction);
		$base_type = $this->head()->get_response_declaration($item, $interaction)->baseType;

		$count = 0;
		$answer = $this->random_value($base_type);
		while($this->head()->get_score($item, $interaction, $answer) != 0 && $count<50){
			$answer = $this->random_value($base_type, ++$count);
		}
		return $count<50 ? $answer : null;
	}

	//END CORRECT-INCORRECT METHODS

}



















?>