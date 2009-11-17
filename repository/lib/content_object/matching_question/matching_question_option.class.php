<?php
/**
 * $Id: matching_question_option.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.matching_question
 */
/**
 * This class represents an option in a matching question.
 */
class MatchingQuestionOption
{
    private $value;
    private $match;
    private $weight;
    private $comment;

    /**
     * Creates a new option for a matching question
     * @param string $value The value of the option
     * @param int $match The index of the match corresponding to this option
     * @param int $weight The weight of this answer in the question
     */
    function MatchingQuestionOption($value, $match, $weight, $comment)
    {
        $this->value = $value;
        $this->match = $match;
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
    function get_match()
    {
        return $this->match;
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