<?php
/**
 * $Id: complex_assessment_rating_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.rating_question
 */
require_once PATH::get_repository_path () . '/question_types/rating_question/complex_rating_question.class.php';

/**
 * This class represents a complex exercise (used to create complex learning objects)
 */
class ComplexAssessmentRatingQuestion extends ComplexRatingQuestion {
	
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