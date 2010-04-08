<?php
/**
 * $Id: matching_question_option.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.question_types.matching_question
 */
/**
 * This class represents an option in a matching question.
 */
class MatchingQuestionOption
{
    const PROPERTY_VALUE = 'value';

	private $value;

    /**
     * Creates a new option for a matching question
     * @param string $value The value of the option
     * @param int $match The index of the match corresponding to this option
     * @param int $weight The weight of this answer in the question
     */
    function MatchingQuestionOption($value, $match)
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