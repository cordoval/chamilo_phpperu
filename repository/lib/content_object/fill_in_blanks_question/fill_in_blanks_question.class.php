<?php
require_once dirname(__FILE__) . '/fill_in_blanks_question_answer.class.php';
/**
 * $Id: fill_in_blanks_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.question_types.fill_in_blanks_question
 */
class FillInBlanksQuestion extends ContentObject
{
    const PROPERTY_ANSWERS = 'answers';
    const PROPERTY_ANSWER_TEXT = 'answer_text';
    const PROPERTY_QUESTION_TYPE = 'question_type';
    
    const TYPE_TEXT = 0;
    const TYPE_SELECT = 1;

    public function add_answer($answer)
    {
        $answers = $this->get_answers();
        $answers[] = $answer;
        return $this->set_additional_property(self :: PROPERTY_ANSWERS, serialize($answers));
    }

    public function set_answers($answers)
    {
        return $this->set_additional_property(self :: PROPERTY_ANSWERS, serialize($answers));
    }

    public function get_answers()
    {
        if ($result = unserialize($this->get_additional_property(self :: PROPERTY_ANSWERS)))
        {
            return $result;
        }
        return array();
    }

    public function get_number_of_answers()
    {
        return count($this->get_answers());
    }

    public function get_answer_text()
    {
        return $this->get_additional_property(self :: PROPERTY_ANSWER_TEXT);
    }

    public function set_answer_text($answer_text)
    {
        $this->set_additional_property(self :: PROPERTY_ANSWER_TEXT, $answer_text);
    }

    public function get_question_type()
    {
        return $this->get_additional_property(self :: PROPERTY_QUESTION_TYPE);
    }

    public function set_question_type($question_type)
    {
        $this->set_additional_property(self :: PROPERTY_QUESTION_TYPE, $question_type);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_ANSWERS, self :: PROPERTY_ANSWER_TEXT, self :: PROPERTY_QUESTION_TYPE);
    }
}
?>