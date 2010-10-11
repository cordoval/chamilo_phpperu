<?php
/**
 * $Id: complex_survey_open_question_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.survey_open_question
 */
require_once PATH :: get_repository_path() . '/question_types/open_question/complex_open_question_form.class.php';
/**
 * This class represents a complex question
 */
class ComplexSurveyOpenQuestionForm extends ComplexOpenQuestionForm
{

public function get_elements() {
		$elements [] = $this->createElement ( 'checkbox', ComplexSurveyOpenQuestion::PROPERTY_VISIBLE, Translation::get ( 'Visible' ) );
		return $elements;
	}
	
	function get_default_values() {
		$cloi = $this->get_complex_content_object_item ();
		
		if (isset ( $cloi )) {
			$defaults [ComplexSurveyOpenQuestion::PROPERTY_VISIBLE] = $cloi->get_visible ();
		}
		
		return $defaults;
	}
	
	// Inherited
	function create_complex_content_object_item() {
		$cloi = $this->get_complex_content_object_item ();
		$values = $this->exportValues ();
		$cloi->set_visible ( $values [ComplexSurveyOpenQuestion::PROPERTY_VISIBLE] );
		return parent::create_complex_content_object_item ();
	}
	
	function create_cloi_from_values($values) {
		$cloi = $this->get_complex_content_object_item ();
		$cloi->set_visible ( $values [ComplexSurveyOpenQuestion::PROPERTY_VISIBLE] );
		return parent::create_complex_content_object_item ();
	}
	
	function update_cloi_from_values($values) {
		$cloi = $this->get_complex_content_object_item ();
		$cloi->set_visible ( $values [ComplexSurveyOpenQuestion::PROPERTY_VISIBLE] );
		return parent::update_complex_content_object_item ();
	}
	
	// Inherited
	function update_complex_content_object_item() {
		$cloi = $this->get_complex_content_object_item ();
		$values = $this->exportValues ();
		$cloi->set_visible ( $values [ComplexSurveyOpenQuestion::PROPERTY_VISIBLE] );
		return parent::update_complex_content_object_item ();
	}
}
?>