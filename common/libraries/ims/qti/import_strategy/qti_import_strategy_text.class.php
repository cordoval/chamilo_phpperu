<?php

/**
 * Import stategy used with textInteractions.
 * 
 * Mainly used to handle regex. For regex the strategy extracts feedbacks and scores directly from the formulas.
 * 
 * Note: a better, more solid, approach would be to generate random string from the regex.  
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class QtiImportStrategyText extends QtiImportStrategyBase{

	public function __construct(QtiRendererBase $renderer, $head){
		parent::__construct($renderer, $head);
	}
	
	public function accept(ImsXmlReader $item){
		$main = $this->get_main_interaction($item);
		$result = $main->is_textEntryInteraction() || $main->is_extendedTextInteraction();
		return $result;
	}

	public function is_case_sensitive(ImsXmlReader $item){
		$interaction = $this->get_main_interaction($item);
		$rules = $this->get_score_rules($item, $interaction);
		foreach($rules as $rule){
			$matches = $rule->all_stringMatch();
			foreach($matches as $match){
				if($match->caseSensitive == 'true'){
					return 1; //cannot return false;
				}
			}
			$matches = $rule->all_equal();
			if(count($matches)>0){
				return 1;
			}
		}
		return 0;
	}
	
	public function get_feedbacks(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $filter_out = array()){
		if(!$answer instanceof ImsXmlReader || ! $answer->is_patternMatch()){
			return false;
		}
		$result = array();
		$matches = $item->all_patternMatch();
		foreach($matches as $match){
			$if = $match->get_parent();
			if($this->is_set_feedback_formula($if) && $match->pattern == $answer->pattern){
				$child = $if->get_setOutcomeValue()->children_head();
				$feedback_id = $this->execute_formula($child);
				$feedback = $item->get_by_id($feedback_id);
				if($feedback->is_modalFeedback()){
					$this->get_renderer()->set_outcome($feedback->outcomeIdentifier, $feedback_id);
					$result[] = $this->render($feedback);
				}
			}
		}
		return empty($result) ? false : $result;
	}
	
	protected function is_set_feedback_formula($response){
		if(!$response->is_responseIf() && !$response->is_responseElseIf()){
			return false;
		}
		if($response->get_setOutcomeValue()->identifier != 'FEEDBACK'){
			return false;
		}
		return true;
	}
	
	public function get_outcome(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $outcome_id = ''){
		if(!$answer instanceof ImsXmlReader || ! $answer->is_patternMatch()){
			return false;
		}
		if(empty($outcome_id)){
			$outcome_id = $this->head()->get_score_outcome_declaration($item)->identifier;
		}
		
		$result = array();
		$response_id = $this->head()->get_response_declaration($item, $interaction)->identifier;
		$rules = $this->get_score_rules($item, $interaction);
		foreach($rules as $rule){
			$condition = $rule->children_head();
			if(	$condition->is_patternMatch() && 
				$condition->get_variable()->identifier == $response_id && 
				$answer->pattern == $condition->pattern && 
				$rule->get_setOutcomeValue()->identifier == $outcome_id){
					$score = $rule->get_setOutcomeValue()->children_head();
					$result = $this->execute_formula($score);
			}
		}
		return empty($result) ? false : $result;
		
	}
		
	/**
	 * Returns expressions used to compare against the interaction's response for score processing.
	 * @param $item
	 * @param $interaction
	 */
	public function get_score_formulas(ImsXmlReader $item, ImsXmlReader $interaction){
		$result = array();
		$response_id = $this->head()->get_response_declaration($item, $interaction)->identifier;
		$response_pattern = './/def:variable[@identifier="'.$response_id.'"]';
		$rules = $this->get_score_rules($item, $interaction);
		foreach($rules as $rule){
			$condition = $rule->children_head();
			if($condition->is_equal() || $condition->is_stringMatch()){
				$branches = $condition->children();
				$has_response[0] = $branches[0]->exist($response_pattern) || ($branches[0]->is_variable() && $branches[0]->identifier == $response_id);
				$has_response[1] = $branches[1]->exist($response_pattern) || ($branches[1]->is_variable() && $branches[1]->identifier == $response_id);				
				if(!$has_response[0] && $has_response[1]){
					$result[] = $branches[0];
				}else if($has_response[0] && !$has_response[1]){
					$result[] = $branches[1];
				}else{
					;
				}
			}else if($condition->is_patternMatch() && $condition->get_variable()->identifier == $response_id){
				$result[] = $condition;
			}
		}
		return $result;
	}
	
	
}



















?>