<?php
/**
 * $Id: multiple_choice_question_option.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.multiple_choice_question
 */
/**
 * This class represents an option in a multiple choice question.
 */
class MultipleChoiceQuestionOption
{
    /**
     * The value of the option
     */
    private $value;
    /**
     * Is this a correct answer to the question?
     */
    private $correct;
    /**
     * The weight of this answer in the question
     */
    private $weight;
    
    private $comment;

    /**
     * Creates a new option for a multiple choice question
     * @param string $value The value of the option
     * @param boolean $correct True if the value of this option is a correct
     * answer to the question
     * @param int $weight The weight of this answer in the question
     */
    function MultipleChoiceQuestionOption($value, $correct, $weight, $comment)
    {
        $this->value = $value;
        $this->correct = $correct;
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
     * Determines if this option is a correct answer
     * @return boolean
     */
    function is_correct()
    {
        return $this->correct;
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