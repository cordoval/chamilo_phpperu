<?php
/**
  * @package repository.lib.content_object.match_numeric_question
 */
require_once dirname(__FILE__) . '/main.php'; 

/**
 * This class represents a complex assessment (used to create complex learning objects)
 */
class ComplexAssessmentMatchNumericQuestion extends ComplexContentObjectItem {
	
	const PROPERTY_WEIGHT = 'weight';
	
	static function get_additional_property_names() {
		return array( self::PROPERTY_WEIGHT );
	}
	
	function get_weight() {
		return $this->get_additional_property ( self::PROPERTY_WEIGHT );
	}
	
	function set_weight($value) {
		$this->set_additional_property ( self::PROPERTY_WEIGHT, $value );
	}
}
?>