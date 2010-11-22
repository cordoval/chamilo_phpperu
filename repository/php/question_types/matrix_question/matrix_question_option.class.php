<?php
namespace repository;
/**
 * $Id: matrix_question_option.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.question_types.matrix_question
 */
/**
 * This class represents an option in a matrix question.
 */
class MatrixQuestionOption
{
	const PROPERTY_VALUE = 'value';

    private $value;

    /**
     * Creates a new option for a matrix question
     * @param string $value The value of the option
     * @param int $matches The index of the match corresponding to this option
     */
    function __construct($value = '')
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