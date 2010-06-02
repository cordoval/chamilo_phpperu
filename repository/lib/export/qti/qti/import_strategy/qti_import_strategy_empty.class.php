<?php

class QtiImportStrategyEmpty{
	
	private static $instance = null;
	
	public static function instance(){
		if(empty(self::$instance)){
			self::$instance = new QtiImportStrategyEmpty();
		}
		return self::$intance;
	}
	
	public static function get_main_interaction(ImsXmlReader $item){
		return false;
	}

	public static function is_numeric_interaction($item, $interaction){
		return false;
	}
	
	public function __construct(){;
	}
	
	public function get_renderer(){
		return false;
	}
	
	public function render($item){
		return false;
	}
	
	public function to_html(ImsXmlReader $item){
		return false;
	}
	
	public function to_text(ImsXmlReader $item){
		return false;
	}
	
	public function get_question_text(ImsXmlReader $item){
		return false;
	}
	
	public function get_question_title(ImsXmlReader $item){
		return false;
	}
	
	public function get_feedbacks(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $filter_out = array()){
		return false;
	}
	
	public function get_modal_feedbacks(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $filter_out = array()){
		return false;
	}
	
	public function get_inline_feedbacks(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $filter_out = array()){
		return false;
	}
	
	public function get_general_feedbacks(ImsXmlReader $item){
		return false;
	}
	
	public function get_correct_feedbacks(ImsXmlReader $item, $filter_out=array()){
		return false;
	}
	
	public function get_partiallycorrect_feedbacks(ImsXmlReader $item, $filter_out=array()){
		return false;
	}
	
	public function get_incorrect_feedbacks(ImsXmlReader $item, $filter_out=array()){
		return false;
	}
	
	public function get_children_feedbacks(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $parent){
		return false;
	}
	
	public function get_score(ImsXmlReader $item, ImsXmlReader $interaction, $answer, $outcome_id = ''){
		return false;
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
		
	public function get_score_default(ImsXmlReader $item){
		return false;
	}
	
	public function get_penalty(ImsXmlReader $item){
		return false;
	}
	
	public function get_tolerance(ImsXmlReader $item, ImsXmlReader $interaction=null, $answer=''){
		return false;
	}
		
	public function get_tolerance_type(ImsXmlReader $item, ImsXmlReader $interaction=null, $answer=''){
		return false;
	}
	
	public function get_partial_responses(ImsXmlReader $item, ImsXmlReader $interaction){
		return false;
	}
	
	public function get_template_values(ImsXmlReader $item, $maximum = 100){
		return false;
	}

	public function get_rubricBlock(ImsXmlReader $item, $role = QTI::VIEW_ALL){
		return false;
	}
	
	public function get_score_outcome_declaration(ImsXmlReader $item){
		return false;
	}
	
	public function get_main_response(ImsXmlReader $item){
		return false;
	}
	
	public function get_response_declaration(ImsXmlReader $item, ImsXmlReader $interaction){
		return false;
	}
	
	public function get_score_formulas(ImsXmlReader $item, ImsXmlReader $interaction){
		return false;
	}
	
	public function is_formula($item){
		return false;
	}
		
	public function is_formula_constant(ImsXmlReader $item){
		return false;
	}
	
	public function execute_formula(ImsXmlReader $item){
		return false;
	}
	
	public function get_correct_responses(ImsXmlReader $item, ImsXmlReader $interaction){
		return false;
	}
	
	public function get_possible_responses(ImsXmlReader $item, ImsXmlReader $interaction){
		return false;
	}

	public function get_possible_responses_text(ImsXmlReader $item, ImsXmlReader $interaction){
		return false;
	}

}


















