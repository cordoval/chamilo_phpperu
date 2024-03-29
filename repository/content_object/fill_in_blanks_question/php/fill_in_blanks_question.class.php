<?php
namespace repository\content_object\fill_in_blanks_question;

use common\libraries\Utilities;
use common\libraries\Versionable;

use repository\ContentObject;

require_once dirname(__FILE__) . '/fill_in_blanks_question_answer.class.php';
/**
 * $Id: fill_in_blanks_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.question_types.fill_in_blanks_question
 */
class FillInBlanksQuestion extends ContentObject implements Versionable
{
    private $answers;

    //const PROPERTY_ANSWERS = 'answers';
    const PROPERTY_ANSWER_TEXT = 'answer_text';
    const PROPERTY_QUESTION_TYPE = 'question_type';

    const TYPE_TEXT = 0;
    const TYPE_SELECT = 1;

    const CLASS_NAME = __CLASS__;

    static function get_type_name()
    {
        return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
    }

    /*
    public function add_answer($answer)
    {
        $answers = $this->get_answers();
        $answers[] = $answer;
        return $this->set_additional_property(self :: PROPERTY_ANSWERS, serialize($answers));
    }

    public function set_answers($answers)
    {
        return $this->set_additional_property(self :: PROPERTY_ANSWERS, serialize($answers));
    }*/

    public function get_answers($index = -1)
    {
        $text = $this->get_answer_text();

        if (! $this->answers)
        {
            $this->answers = FillInBlanksQuestionAnswer :: parse($text);
        }

        if ($index < 0)
        {
            $result = $this->answers;
        }
        else
        {
            $result = array();
            foreach ($this->answers as $answer)
            {
                if ($answer->get_position() == $index)
                {
                    $result[] = $answer;
                }
            }
        }
        return $result;
    }

    public function get_best_answer_for_question($index)
    {
        return FillInBlanksQuestionAnswer :: get_best_answer($this->get_answers($index));
    }

    public function get_hint_for_question($index)
    {
        $answer = $this->get_best_answer_for_question($index);
        if ($this->get_question_type() == self :: TYPE_SELECT)
        {
            return $answer->get_hint();
        }
        elseif ($this->get_question_type() == self :: TYPE_TEXT)
        {
            return substr($answer->get_value(), 0, 1);
        }
        else
        {
            return '';
        }
    }

    public function get_number_of_questions()
    {
        $text = $this->get_answer_text();
        return FillInBlanksQuestionAnswer :: get_number_of_questions($text);
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

    /**
     * Returns the maximum weight/score a user can receive.
     */
    public function get_maximum_weight()
    {
        $maximum = array();
        $answers = $this->get_answers();
        foreach ($answers as $answer)
        {
            $position = $answer->get_position();
            $weight = $answer->get_weight();
            if (! isset($maximum[$position]))
            {
                $maximum[$position] = $weight;
            }
            else
            {
                $maximum[$position] = max($maximum[$position], $weight);
            }
        }
        $result = 0;
        foreach ($maximum as $weight)
        {
            $result += $weight;
        }
        return $result;
    }

    /**
     * Returns the maximum weight for a specific question.
     * @param int index question's index
     * @return maximum possible weight for the question
     */
    public function get_question_maximum_weight($index)
    {
        $result = 0;
        $answers = $this->get_answers();
        foreach ($answers as $answer)
        {
            $position = $answer->get_position();
            $weight = $answer->get_weight();
            if ($position == $index)
            {
                $result = max($result, $weight);
            }
        }
        return $result;
    }

    /**
     * Returns true if $answer is correct for question whith postion $question_index.
     * That is if answer receive the maximum score.
     * @param $question_index
     * @param $answer
     * @return true if correct false otherwise
     */
    public function is_correct($question_index, $answer)
    {
        $weight = $this->get_weight_from_answer($answer);
        $max_question_weight = $this->get_question_maximum_weight($question_index);

        return $weight == $max_question_weight;
    }

    public function get_weight_from_answer($question_index, $answer)
    {
        $answers = $this->get_answers();
        foreach ($answers as $a)
        {
            if ($a->get_value() == $answer && $a->get_position() == $question_index)
            {
                return $a->get_weight();
            }
        }

        return 0;
    }

    static function get_additional_property_names()
    {
        return array(
                self :: PROPERTY_ANSWER_TEXT,
                self :: PROPERTY_QUESTION_TYPE);
    }

    public function has_comment()
    {
        foreach ($this->get_answers() as $option)
        {
            if ($option->has_comment())
            {
                return true;
            }
        }

        return false;
    }
}
?>