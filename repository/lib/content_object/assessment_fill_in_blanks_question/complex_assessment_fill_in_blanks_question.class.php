<?php
/**
 * $Id: complex_assessment_fill_in_blanks_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.fill_in_blanks_question
 */
require_once PATH::get_repository_path () . '/question_types/fill_in_blanks_question/complex_fill_in_blanks_question.class.php';

/**
 * This class represents a complex assessment (used to create complex learning objects)
 */
class ComplexAssessmentFillInBlanksQuestion extends ComplexFillInBlanksQuestion {
	
	const PROPERTY_WEIGHT = 'weight';
	
	static function get_additional_property_names() {
		return array (self::PROPERTY_WEIGHT );
	}
	
	function get_weight() {
		return $this->get_additional_property ( self::PROPERTY_WEIGHT );
	}
	
	function set_weight($value) {
		$this->set_additional_property ( self::PROPERTY_WEIGHT, $value );
	}
}
?>