<?php

require_once PATH::get_repository_path() . 'lib/content_object/survey_fill_in_blanks_question/survey_fill_in_blanks_question_answer.class.php';
require_once PATH::get_repository_path() . 'lib/content_object/assessment_fill_in_blanks_question/assessment_fill_in_blanks_question_answer.class.php';


/**
 * $Id: fill_in_blanks_question_answer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.question_types.fill_in_blanks_question
 */
class FillInBlanksQuestionAnswer
{
    private $value;
    private $weight;
    private $comment;
    private $size;
    private $position;

    function FillInBlanksQuestionAnswer($value, $weight, $comment, $size, $position)
    {
        $this->value = $value;
        $this->weight = $weight;
        $this->comment = $comment;
        $this->size = $size;
        $this->position = $position;
    }

    function get_comment()
    {
        return $this->comment;
    }

    function get_value()
    {
        return $this->value;
    }

    function get_weight()
    {
        return $this->weight;
    }

    function get_size()
    {
        return $this->size;
    }

    function get_position()
    {
        return $this->position;
    }
}
?>