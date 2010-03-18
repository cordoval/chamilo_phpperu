<?php
/**
 * $Id: complex_survey_fill_in_blanks_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.survey_fill_in_blanks_question
 */
require_once PATH::get_repository_path () . '/question_types/fill_in_blanks_question/complex_fill_in_blanks_question.class.php';
/**
 * This class represents a complex assessment (used to create complex learning objects)
 */
class ComplexSurveyFillInBlanksQuestion extends ComplexFillInBlanksQuestion {
	
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
}
?>