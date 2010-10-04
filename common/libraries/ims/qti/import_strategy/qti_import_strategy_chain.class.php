<?php

/**
 * Chain of responsibility for import strategies.
 * Method calls are delegated on each strategy present in the chain until one returns a meaningful value.
 * A meaningful value is a value which is not exactly equal to boolean false (===).
 * If no strategy returns a meaningful value the chain delegates the call to the failover strategy which returns a default value.
 *
 * @copyright (c) 2010 University of Geneva
 * @author laurent.opprecht@unige.ch
 *
 */
class QtiImportStrategyChain extends QtiImportStrategyBase{

	private $chain = array();
	private $failover = null;

	public function __construct(QtiRendererBase $renderer){
		parent::__construct($renderer, $this);
		$this->failover = new QtiImportStrategyFailover($renderer, $this);
	}

	public function add_strategy(QtiImportStrategyBase $strategy){
		$this->chain[] = $strategy;
		$strategy->set_renderer($this->get_renderer());
		$strategy->set_head($this);
	}

	protected function delegate($name, $args){
		foreach($this->chain as $strategy){
			$f = array($strategy, $name);
			$item = reset($args);
			$accepted = !($item instanceof ImsXmlReader) || $strategy->accept($item);

			if($accepted && is_callable($f)){
				try{
					$result = call_user_func_array($f, $args);
					if($result !== false){
						return $result;
					}
				}catch(Exception $e){
					debug($e);
				}
			}
		}
		$f = array($this->failover, $name);
		if(is_callable($f)){
			$result = call_user_func_array($f, $args);
			return $result;
		}
		return false;
	}

	public function __call($name, $arguments){
		return $this->delegate($name, $arguments);
	}

	//public function get_renderer()
	//public function render($item)
	//public function to_html(ImsXmlReader $item)
	//public function to_text(ImsXmlReader $item)
	//public function is_formula($item)
	//public function is_formula_constant(ImsXmlReader $item)

	public function get_question_text(ImsXmlReader $item){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_question_title(ImsXmlReader $item){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_feedbacks(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $filter_out = array()){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_modal_feedbacks(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $filter_out = array()){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_inline_feedbacks(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $filter_out = array()){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_general_feedbacks(ImsXmlReader $item){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_correct_feedbacks(ImsXmlReader $item, $filter_out=array()){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_partiallycorrect_feedbacks(ImsXmlReader $item, $filter_out=array()){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_incorrect_feedbacks(ImsXmlReader $item, $filter_out=array()){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_children_feedbacks(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $parent){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_score(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $outcome_id = ''){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_outcome(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $outcome_id = ''){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_scores(ImsXmlReader $item, $responses, $interpreter = null){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_minimum_score(ImsXmlReader $item){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_maximum_score(ImsXmlReader $item, $interaction = null){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	protected function get_maximum_score_possible_answers(ImsXmlReader $item){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_score_default(ImsXmlReader $item){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_penalty(ImsXmlReader $item){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_tolerance(ImsXmlReader $item, ImsXmlReader $interaction=null, $answer=''){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_tolerance_type(ImsXmlReader $item, ImsXmlReader $interaction=null, $answer=''){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_partial_responses(ImsXmlReader $item, ImsXmlReader $interaction){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_template_values(ImsXmlReader $item, $maximum = 100){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_rubricBlock(ImsXmlReader $item, $role = QTI::VIEW_ALL){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_score_outcome_declaration(ImsXmlReader $item){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_main_response(ImsXmlReader $item){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_response_declaration(ImsXmlReader $item, ImsXmlReader $interaction){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_score_formulas(ImsXmlReader $item, ImsXmlReader $interaction){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function execute_formula(ImsXmlReader $item, ImsXmlReader $formula){
		$args = func_get_args();
		$result = $this->delegate(__FUNCTION__, $args);
		return $result;
	}

	public function get_correct_responses(ImsXmlReader $item, ImsXmlReader $interaction){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_possible_responses(ImsXmlReader $item, ImsXmlReader $interaction){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

	public function get_possible_responses_text(ImsXmlReader $item, ImsXmlReader $interaction){
		$args = func_get_args();
		return $this->delegate(__FUNCTION__, $args);
	}

}


















