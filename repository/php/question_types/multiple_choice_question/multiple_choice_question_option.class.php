<?php
/**
 * $Id: survey_multiple_choice_question_option.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.question_types.multiple_choice_question
 */
/**
 * This class represents an option in a multiple choice question.
 */
class MultipleChoiceQuestionOption
{
    const PROPERTY_VALUE = 'value';
    
    private $value;
    
    function MultipleChoiceQuestionOption($value)
    {
		$this->value = $value;
	}
    
    function get_value()
    {
    	return $this->value;
    }
}
?>