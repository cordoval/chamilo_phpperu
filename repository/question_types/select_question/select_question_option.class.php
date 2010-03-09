<?php
/**
 * $Id: select_question_option.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.question_types.select_question
 */
/**
 * This class represents an option in a multiple choice question.
 */
class SelectQuestionOption
{
	const PROPERTY_VALUE = 'value';
    
    private $value;  
	
    /**
     * Creates a new option for a multiple choice question
     * @param string $value The value of the option
     * @param boolean $correct True if the value of this option is a correct
     * answer to the question
     */
    function SelectQuestionOption($value)
    {
    	$this->value = $value;
    }
    
     /**
     * Gets the value of this option
     * @return string
     */
    function get_value()
    {
    	return $this->value;
    }
}
?>