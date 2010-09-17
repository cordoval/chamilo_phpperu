<?php

/**
 * Failover/default strategy.
 * Meant to be called if no other strategy is able to extract a meaningful value.
 * Returns acceptable default values for the base class interface.
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class QtiImportStrategyFailover extends QtiImportStrategyBase{

	public function __construct(QtiRendererBase $renderer, $head){
		parent::__construct($renderer, $head);
	}
	
	public function get_question_text(ImsXmlReader $item){
		return '';
	}
	
	public function get_question_title(ImsXmlReader $item){
		return '';
	}
	
	public function get_feedbacks(ImsQtiReader $item, ImsXmlReader $interaction, $answer, $filter_out = array()){
		return array();
	}
	
	public function get_modal_feedbacks(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $filter_out = array()){
		return array();
	}
	
	public function get_inline_feedbacks(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $filter_out = array()){
		return array();
	}
	
	public function get_general_feedbacks(ImsXmlReader $item){
		return array();
	}
	
	public function get_correct_feedbacks(ImsXmlReader $item, $filter_out=array()){
		return array();
	}

	public function get_partiallycorrect_feedbacks(ImsXmlReader $item, $filter_out=array()){
		return array();
	}
	
	public function get_incorrect_feedbacks(ImsXmlReader $item, $filter_out=array()){
		return array();
	}
	
	public function get_children_feedbacks(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $parent){
		return array();
	}
	
	public function get_score(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $outcome_id = ''){
		return 0;
	}
		
	public function get_outcome(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $outcome_id = ''){
		return null;
	}

	public function get_scores(ImsQtiReader $item, $responses, $interpreter = null){
		return array();
	}
	
	public function get_minimum_score(ImsXmlReader $item){
		return 0;
	}
	
	public function get_maximum_score(ImsXmlReader $item, $interaction = null){
		return 1;
	}

	public function get_maximum_score_possible_answers(ImsXmlReader $item){
		return array();
	}
	
	public function get_score_default(ImsXmlReader $item){
		return 0;
	}
	
	public function get_penalty(ImsXmlReader $item){
		return 0;
	}
		
	public function get_tolerance(ImsXmlReader $item, ImsXmlReader $interaction=null, $answer=''){
		return 0;
	}
		
	public function get_tolerance_type(ImsXmlReader $item, ImsXmlReader $interaction=null, $answer=''){
		return Qti::TOLERANCE_MODE_EXACT;
	}
	
	public function get_partial_responses(ImsXmlReader $item, ImsXmlReader $interaction){
		return array();
	}
	
	public function get_template_values(ImsXmlReader $item, $maximum = 100){
		return array();
	}

	public function get_rubricBlock(ImsXmlReader $item, $role = QTI::VIEW_ALL){
		return array();
	}
	
	public function list_outcome(ImsXmlReader $item, $include_feedback_outcome = false){
		return array();
	}
	
	public function get_score_outcome_declaration(ImsXmlReader $item){
		return $item->get_default_result();
		
	}
	
	public function get_main_response(ImsXmlReader $item){
		return $item->get_default_result();
	}
	
	public function get_response_declaration(ImsXmlReader $item, ImsXmlReader $interaction){
		return $item->get_default_result();
	}
	
	public function get_score_formulas(ImsXmlReader $item, ImsXmlReader $interaction){
		return array();
	}
	
	public function is_case_sensitive(ImsXmlReader $item){
		return 0;
	}
	
	public function is_formula($item){
		return false;
	}
		
	public function is_formula_constant(ImsXmlReader $item){
		return true;
	}
	
	public function execute_formula(ImsXmlReader $item){
		return null;
	}
	
	public function get_correct_responses(ImsXmlReader $item, ImsXmlReader $interaction){
		return array();
	}
	
	public function get_possible_responses(ImsXmlReader $item, ImsXmlReader $interaction){
		return array();
	}

	public function get_possible_responses_text(ImsXmlReader $item, ImsXmlReader $interaction){
		return array();
	}
}



















?>