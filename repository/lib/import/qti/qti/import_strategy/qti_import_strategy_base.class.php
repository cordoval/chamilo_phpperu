<?php

/**
 * Base class for import strategies.
 * Strategies are meant to be chained in a chain of responsibility:
 * 
 * 	- methods shall returns false if a strategy is not able to extract a value or the method shall not be implemented at all.
 *  - methods unable to return a value should not return a default. This responsibility is left to the faileover strategy at the end of the chain.
 * 	- to maintain identity methods called from a strategy should be done on the head to ensure to whole chain is traversed.
 * 	  That is 
 * 		$this->head()->my_method() should be called instead of $this->my_method()
 * 	  if the method is not specific to the strategy but may be implemented by another strategy as well.
 * 	- the most generic strategies should be kept at the end of the chain to ensure the most specific strategy is used.
 *  - the chain delegate every call on the chain so the interface is not limited to the base class.
 *  - a function returning a boolean value should not return false but 0 instead. This is required because false is already used to signal that a method is unable to return a value.   	 
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class QtiImportStrategyBase{
	
	const DEFAULT_MAXIMUM_SCORE = 1;
	const DEFAULT_SCORE = 0;
		
	/**
	 * 
	 * @param ImsXmlReader $item
	 * @return ImsQtiReader
	 */
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
		if(	!$interaction->is_sliderInteraction() && 
			!$interaction->is_textEntryInteraction() && 
			!$interaction->is_extendedTextInteraction()){
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
	
	public static function has_score($item){
		$strategy = self::create_moodle_default_strategy(new QtiPartialRenderer(new QtiImportResourceManager('', '')));
		$var = $strategy->get_score_outcome_declaration($item);		
		return $var !== false && $var->identifier != '';
	}
	
	public static function has_answers($item, $interaction = null){
		$strategy = self::create_moodle_default_strategy(new QtiPartialRenderer(new QtiImportResourceManager('', '')));
		$interaction = empty($interaction) ? $strategy->get_main_interaction($item) : $interaction;
		$result = $strategy->get_possible_responses($item, $interaction);
		return $result !== false && count($result)>0;
	} 
	
	public static function has_label($item, $element, $key, $value){
		$label = $element->label;
		$pairs = explode(';', $label);
		foreach($pairs as $pair){
			$entry = explode('=', $pair);
			if(count($entry)==2){
				$key_ = reset($entry);
				$value_ = trim($entry[1]);
				if($key_==$key && $value_ ==$value){
					return true;
				}
			}
		}
		return false;
	}
	
	public static function create_moodle_default_strategy($renderer){
		$result = new QtiImportStrategyChain($renderer);
		$strategy = new QtiMoodleReimportStrategy($renderer, $result);
		$result->add_strategy($strategy);
		$strategy = new QtiImportStrategyText($renderer, $result);
		$result->add_strategy($strategy);
		$strategy = new QtiImportStrategyGeneric($renderer, $result);
		$result->add_strategy($strategy);
		return $result;
	}
	
	/**
	 * 
	 * @var QtiRendererBase
	 */
	private $renderer = null;
	
	/**
	 * 
	 * @var QtiImportStrategyBase
	 */
	private $head = null;
	
	public function __construct(QtiRendererBase $renderer, QtiImportStrategyBase $head){
		$this->renderer = $renderer;
		$this->head = $head;
	}

	public function head(){
		return $this->head;
	}
	
	public function get_head(){
		return $this->head;
	}
	
	public function set_head($value){
		$result = $this->head;
		$this->head = $value;
		return $result;
	}
	
	public function accept(ImsXmlReader $item){
		return true;
	}
	
	public function reset(){
		$this->get_renderer()->reset_outcomes();
	}
	
	//RENDERER
	
	/**
	 * @return QtiRendererBase
	 */
	public function get_renderer(){
		return $this->renderer;
	}
	
	public function set_renderer($value){
		$result = $this->renderer;
		$this->renderer = $value;
		return $result;
	}
	
	public function render($item){
		if(is_array($item)){
			$result = array();
			foreach($item as $el){
				$result[] = $this->to_html($el);
			}
			return $result;
		}else{
			return $this->to_html($item);
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
		return false;
	}
	
	public function get_question_title(ImsXmlReader $item){
		return false;
	}
	
	//FEEDBACKS 
	
	public function get_feedbacks(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $filter_out = array()){
		return false;
	}
	
	public function get_modal_feedbacks(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $filter_out = array()){
		return false;
	}
	
	public function get_inline_feedbacks(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $filter_out = array()){
		return false;
	}
	
	/**
	 * I.e. feedbacks that are always output
	 * @param ImsXmlReader $item
	 * @return array
	 */
	public function get_general_feedbacks(ImsXmlReader $item){
		return false;
	}

	/**
	 * Feedback for any correct response
	 * @param ImsXmlReader $item
	 * @param array $filter_out
	 * @return array
	 */
	public function get_correct_feedbacks(ImsXmlReader $item, $filter_out=array()){
		return false;
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
		return false;
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
		return false;
	}
	
	//END FEEDBACKS
	
	//SCORE
	
	public function get_score(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $outcome_id = ''){
		return $this->get_outcome($item, $interaction, $answer, $outcome_id);
	}
		
	public function get_outcome(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $outcome_id = ''){
		return false;
	}

	public function get_scores(ImsXmlReader $item, $responses, $interpreter = null){
		return false;
	}
	
	public function get_minimum_score(ImsXmlReader $item){
		return false;
	}
	
	public function get_maximum_score(ImsXmlReader $item, $interaction = null){
		return false;
	}
	
	protected function get_maximum_score_possible_answers(ImsXmlReader $item){
		return false;
	}
	
	public function get_score_default(ImsXmlReader $item){
		return false;
	}
	
	public function get_penalty(ImsXmlReader $item){
		return false;
	}
	
	//END SCORE
	
	public function get_tolerance(ImsXmlReader $item, ImsXmlReader $interaction=null, $answer=''){
		return false;
	}
	
	/**
	 * 
	 * @param $item
	 * @param $interaction
	 */
	public function get_partial_responses(ImsXmlReader $item, ImsXmlReader $interaction){
		return false;
	}
	
	public function get_template_values(ImsXmlReader $item, $maximum = 100){
		return false;
	}

	public function get_rubricBlock(ImsXmlReader $item, $role = QTI::VIEW_ALL){
		return false;
	}
	/*
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
	}*/
	
	/**
	 * 
	 * @param ImsXmlReader $item
	 * @return ImsQtiReader
	 */
	public function get_score_outcome_declaration(ImsXmlReader $item){
		return false;
	}
	
	public function get_main_response(ImsXmlReader $item){
		return $item->get_by_id(Qti::RESPONSE);
	}

	public function get_response_declaration(ImsXmlReader $item, ImsXmlReader $interaction){
		return $item->get_child_by_id($interaction->responseIdentifier);
	}
	
	public function get_declaration(ImsXmlReader $item, $id){
		return $item->get_child_by_id($id);
	}
	
	//FORMULA
	
	/**
	 * Returns expressions used to compare against the interaction's response for score processing.
	 * I.e. the part of the reponse rules which is used to compute the score.
	 * @param $item
	 * @param $interaction
	 */
	public function get_score_formulas(ImsXmlReader $item, ImsXmlReader $interaction){
		return false;
	}
	
	public function is_formula($item){
		return $item instanceof ImsXmlReader;
	}
		
	public function is_formula_constant(ImsXmlReader $item){
		return count($item->all_variable()) == 0; 
	}
	
	public function execute_formula(ImsXmlReader $item){
		$interpreter = new QtiInterpreter();
		$result = $interpreter->execute($item);
		return $result;
	}
	
	protected function get_score_formula_from_condition(ImsXmlReader $condition, $outcome_id, $score_id){
		return false;
	}

	/**
	 * Returns an array filled with if/elseif reponse rules which changes the score based on the interaction response
	 * @param ImsXmlReader $item
	 * @param ImsXmlReader $interaction
	 */
	protected function get_score_rules(ImsXmlReader $item, ImsXmlReader $interaction){
		
		$result = array();
		$outcome_id = $this->head()->get_response_declaration($item, $interaction)->identifier;
		$score_id = $this->head()->get_score_outcome_declaration($item)->identifier;
		$conditions = $item->get_responseProcessing()->list_responseCondition();
		foreach($conditions as $condition){
			$formulas = $this->get_score_if_from_condition($item, $condition, $outcome_id, $score_id);
			$result = array_merge($result, $formulas);
		}
		return $result;
	}
	
	/**
	 * Helper function for get_score_rules. Extract the if/elseif which set a score from a condition
	 * @param $condition
	 * @param $outcome_id
	 * @param $score_id
	 */
	private function get_score_if_from_condition(ImsXmlReader $item, ImsXmlReader $condition, $outcome_id, $score_id){
		$result = array();
		$ifs = array_merge($condition->list_responseIf(), $condition->list_responseElseIf());
		foreach($ifs as $if){
			if($this->is_set_score_if($item, $if, $outcome_id, $score_id)){
				$result[] = $if;
			}
		}
		return $result;
	}
	
	/**
	 * Helper function for get_score_rules. Returns true if the if/elseif sets the score based on the response.
	 * @param ImsXmlReader $if
	 * @param unknown_type $response_id
	 * @param unknown_type $score_id
	 */
	private function is_set_score_if(ImsXmlReader $item, ImsXmlReader $if, $response_id, $score_id){
		if(!$if->is_responseIf() && !$if->is_responseElseIf()){
			return false;
		}
		$base_type = $this->get_declaration($item, $if->get_setOutcomeValue()->identifier)->baseType;
		if($base_type != Qti::BASETYPE_FLOAT && $base_type != Qti::BASETYPE_INTEGER){
			return false;
		}
		$response_pattern = './/def:variable[@identifier="'.$response_id.'"]';
		$branches = $if->children();
		$condition = $branches[0];
		$response_rule = $branches[1];
		if(!$condition->exist($response_pattern) || $response_rule->exist($response_pattern)){
			return false;
		}
		
		if($condition->is_equal() || $condition->is_stringMatch()){
			$branches = $condition->children();
			$has_response[0] = $branches[0]->exist($response_pattern) || ($branches[0]->is_variable() && $branches[0]->identifier == $response_id);
			$has_response[1] = $branches[1]->exist($response_pattern) || ($branches[1]->is_variable() && $branches[1]->identifier == $response_id);
			if(!$has_response[0] && $has_response[1]){
				return true;
			}else if($has_response[0] && !$has_response[1]){
				return true;
			}else{
				return false;
			}
		}else if($condition->is_patternMatch() && $condition->get_variable()->identifier == $response_id){
			return true;
		}
		return false;
	}
	
	
	
	
	//END FORMULAS
	
	//CORRECT-INCORRECT METHODS
	
	public function get_correct_responses(ImsXmlReader $item, ImsXmlReader $interaction){
		return false;
	}
	
	public function get_possible_responses(ImsXmlReader $item, ImsXmlReader $interaction){
		return false;
	}

	public function get_possible_responses_text(ImsXmlReader $item, ImsXmlReader $interaction){
		return false;
	}

	
	protected function set_correct_responses(ImsXmlReader $item, $interpreter){
		$interactions = $item->list_interactions();
		foreach($interactions as $interaction){
			$correct_responses = $this->head()->get_correct_responses($item, $interaction);				
			$id = $interaction->responseIdentifier;
			$cardinality = $this->get_response_declaration($item, $interaction)->cardinality;
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
	
	protected function get_incorrect_response(ImsXmlReader $item, $interaction){
		return false;
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


















