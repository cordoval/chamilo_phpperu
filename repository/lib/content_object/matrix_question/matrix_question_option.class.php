<?php
/**
 * $Id: matrix_question_option.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.matrix_question
 */
/**
 * This class represents an option in a matrix question.
 */
class MatrixQuestionOption
{
    private $value;
    private $matches;
    private $weight;
    private $comment;

    /**
     * Creates a new option for a matrix question
     * @param string $value The value of the option
     * @param int $match The index of the match corresponding to this option
     * @param int $weight The weight of this answer in the question
     */
    function MatrixQuestionOption($value = '', $matches = array(), $weight = 1, $comment = '')
    {
        $this->value = $value;
        $this->matches = $matches;
        $this->weight = $weight;
        $this->comment = $comment;
    }

    /**
     * Gets the value of this option
     * @return string
     */
    function get_value()
    {
        return $this->value;
    }

    /**
     * Gets the index of the match corresponding to this option
     * @return int
     */
    function get_matches()
    {
        return unserialize($this->matches);
    }

    /**
     * Gets the weight of this answer
     */
    function get_weight()
    {
        return $this->weight;
    }

    function get_comment()
    {
        return $this->comment;
    }
}
?>