<?php
/**
 * $Id: complex_survey_multiple_choice_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.survey_multiple_choice_question
 */
require_once PATH::get_repository_path () . '/question_types/multiple_choice_question/complex_multiple_choice_question.class.php';

/**
 * This class represents a complex assessment (used to create complex learning objects)
 */
class ComplexSurveyMultipleChoiceQuestion extends ComplexMultipleChoiceQuestion {
	
	const PROPERTY_VISIBLE = 'visible';
	
	static function get_additional_property_names() {
		return array (self::PROPERTY_VISIBLE );
	}
	
	function get_visible() {
		return $this->get_additional_property ( self::PROPERTY_VISIBLE );
	}
	
	function set_visible($value) {
		$this->set_additional_property ( self::PROPERTY_VISIBLE, $value );
	}
	
	function toggle_visibility() {
		$this->set_visible ( ! $this->get_visible () );
	}

}
?>