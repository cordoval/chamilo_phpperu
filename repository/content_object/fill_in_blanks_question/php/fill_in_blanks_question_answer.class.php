<?php
namespace repository\content_object\fill_in_blanks_question;

/**
 * $Id: fill_in_blanks_question_answer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.question_types.fill_in_blanks_question
 */
class FillInBlanksQuestionAnswer
{

    /**
     * If format is:
     * [answer(feedback)=score,answer]{hint}
     * then use this for regex
     * \[([^\[\]]*)\](?:\{([^\[\}]*)\})?
     *
     * And to split individual questions into answers
     * with feedback and a score use
     * (?:([^,\n\r\(\\=)]+)(?:\(([^,\n\r]+)\))?(?:=([0-9]+))?,?)+?
     *
     */
    const CLOZE_REGEX = '/\[[^\[\]]*\]/';
    const QUESTIONS_REGEX = '/\[([^[\]]*)\](?:\{([^[}]*)\})?/';
    const PARTS_REGEX = '/(?:([^,\n\r(\\\\=)]+)(?:\(([^,\n\r]+)\))?(?:=([0-9]+))?,?)+?/';

    /**
     *
     * @param string $text formats question [answer 1(feedback 1)=score 1, answer 2=score 2, answer 3(feedback 3)] question 2 [answer 1, answer 2].
     * @return array of question's answers
     */
    static function parse($text)
    {
        $result = array();

        $questions = array();
        preg_match_all(self :: QUESTIONS_REGEX, $text, $questions);

        foreach ($questions[1] as $question_id => $question)
        {
            $answers = array();
            preg_match_all(self :: PARTS_REGEX, $question, $answers);

            foreach ($answers[1] as $answer_id => $answer)
            {
                $score = is_numeric($answers[3][$answer_id]) ? $answers[3][$answer_id] : 1;
                $result[] = new FillInBlanksQuestionAnswer($answer, $score, $answers[2][$answer_id], '', $question_id, $questions[2][$question_id]);
            }
        }
        return $result;
    }

    /**
     * Get the best possible answer for a question,
     * based on it's weight / score
     * @param array $answers
     * @return null|FillInBlanksQuestionAnswer
     */
    static function get_best_answer(array $answers)
    {
        $best_weight = 0;
        $best_answer = null;

        foreach ($answers as $key => $answer)
        {
            if ($answer->get_weight() > $best_weight)
            {
                $best_weight = $answer->get_weight();
                $best_answer = $answer;
            }
        }

        return $best_answer;
    }

    static function format($answer)
    {
        //@todo: if needed
    }

    static function get_number_of_questions($text)
    {
        $matches = array();
        return preg_match_all(self :: QUESTIONS_REGEX, $text, $matches);
    }

    private $value;
    private $weight;
    private $comment;
    private $hint;
    private $size;
    private $position;

    function __construct($value, $weight, $comment, $size, $position, $hint)
    {
        $this->value = $value;
        $this->weight = $weight;
        $this->comment = $comment;
        $this->hint = $hint;
        $this->size = empty($size) ? strlen($value) : $size;
        $this->position = $position;
    }

    function get_comment()
    {
        return $this->comment;
    }

    function get_hint()
    {
        return $this->hint;
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