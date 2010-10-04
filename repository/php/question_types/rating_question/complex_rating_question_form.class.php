<?php
/**
 * $Id: complex_rating_question_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.question_types.rating_question
 */
require_once dirname ( __FILE__ ) . '/complex_rating_question.class.php';
/**
 * This class represents a complex question
 */
class ComplexRatingQuestionForm extends ComplexContentObjectItemForm {
	
	protected function build_creation_form() {
		parent::build_creation_form ();
		$elements = $this->get_elements ();
		foreach ( $elements as $element ) {
			$this->addElement ( $element );
		}
	}
	
	// Inherited
	protected function build_editing_form() {
		parent::build_editing_form ();
		$elements = $this->get_elements ();
		foreach ( $elements as $element ) {
			$this->addElement ( $element );
		}
	}
	
	function setDefaults($defaults = array ()) {
		$defaults = array_merge ( $defaults, $this->get_default_values () );
		parent::setDefaults ( $defaults );
	}

}
?>