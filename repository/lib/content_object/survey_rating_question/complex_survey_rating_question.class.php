<?php
/**
 * $Id: complex_survey_rating_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.survey_rating_question
 */
require_once PATH::get_repository_path () . '/question_types/rating_question/complex_rating_question.class.php';

/**
 * This class represents a complex exercise (used to create complex learning objects)
 */
class ComplexSurveyRatingQuestion extends ComplexRatingQuestion {
	
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