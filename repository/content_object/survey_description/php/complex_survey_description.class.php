<?php
/**
 * $Id: complex_survey_description.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.survey_description
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */

class ComplexSurveyDescription extends ComplexContentObjectItem {
	
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
	
    function is_visible()
    {
        return $this->get_visible() == 1;
    }
	
	function toggle_visibility() {
		$this->set_visible ( ! $this->get_visible () );
	}
}
?>