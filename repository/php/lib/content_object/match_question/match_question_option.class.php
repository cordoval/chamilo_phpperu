<?php
/**
 * $Id: match_question_option.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.match_question
 */
/**
 * This class represents an option in a match question.
 */
class MatchQuestionOption
{
    /**
     * The value of the option
     */
    private $value;
    /**
     * The weight of this answer in the question
     */
    private $weight;
    
    private $comment;

    /**
     * Creates a new option for a match question
     * @param string $value The value of the option
     * @param boolean $correct True if the value of this option is a correct
     * answer to the question
     * @param int $weight The weight of this answer in the question
     */
    function MatchQuestionOption($value, $weight, $comment)
    {
        $this->value = $value;
        $this->weight = $weight;
        $this->comment = $comment;
    }

    function get_comment()
    {
        return $this->comment;
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
     * Gets the weight of this answer
     */
    function get_weight()
    {
        return $this->weight;
    }
}
?>