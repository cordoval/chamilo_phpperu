<?php
namespace repository\content_object\assessment;

use common\libraries\Translation;

use repository\content_object\fill_in_blanks_question\FillInBlanksQuestion;
use repository\content_object\fill_in_blanks_question\FillInBlanksQuestionAnswer;

/**
 * $Id: fill_in_blanks_question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.question_result_display
 */
require_once dirname(__FILE__) . '/../question_result_display.class.php';

class FillInBlanksQuestionResultDisplay extends QuestionResultDisplay
{
    /**
     * @var string
     */
    private $parts;

    /**
     * @var array
     */
    private $feedback_answer = array();

    /**
     * @var boolean
     */
    private $has_feedback;

    function get_question_result()
    {
        $answers = $this->get_answers();

        $answer_text = $this->get_question()->get_answer_text();
        $answer_text = nl2br($answer_text);
        $this->parts = preg_split(FillInBlanksQuestionAnswer :: QUESTIONS_REGEX, $answer_text);

        $html[] = '<div class="with_borders">';
        $html[] = array_shift($this->parts);
        $index = 0;
        foreach ($this->parts as $i => $part)
        {
            $answers[$i] = empty($answers[$i]) ? Translation :: get('NoAnswer') : $answers[$i];

            $weight = $this->get_question()->get_weight_from_answer($i, $answers[$i]);
            $max_question_weight = $this->get_question()->get_question_maximum_weight($i);

            if ($weight == $max_question_weight)
            {
                $html[] = '<span style="color:green"><b>' . $answers[$i] . '</b></span>';
            }
            else
            {
                if ($weight == 0)
                {
                    $html[] = '<span style="color:red"><b>' . $answers[$i] . '</b></span>';
                }
                else
                {
                    $html[] = '<span style="color:orange"><b>' . $answers[$i] . '</b></span>';
                }
            }

            $html[] = $part;
            $index ++;
        }

        $html[] = '</div>';

        //        $html[] = '<div class="splitter"><b>' . Translation :: get('Questions') . '</b></div>';
        $html[] = '<table class="data_table take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="list">#</th>';
        $html[] = '<th class="list">' . Translation :: get('Answer') . '</th>';

        if ($this->get_assessment_result_processor()->get_assessment_viewer()->display_textual_feedback() && $this->has_feedback())
        {
            $html[] = '<th class="list">' . Translation :: get('Feedback') . '</th>';
        }

        if ($this->get_assessment_result_processor()->get_assessment_viewer()->display_numeric_feedback())
        {
            $html[] = '<th class="list">' . Translation :: get('Score') . '</th>';
        }

        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';

        foreach ($this->parts as $index => $part)
        {
            $html[] = $this->get_question_feedback($index, $answers[$index]);
        }

        $html[] = '</tbody>';
        $html[] = '</table>';

        return implode("\n", $html);
    }

    function get_question_feedback($index, $answer)
    {

        $html[] = '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">';
        $html[] = '<td>' . ($index + 1) . '</td>';

        $weight = $this->get_question()->get_weight_from_answer($index, $answer);
        $max_question_weight = $this->get_question()->get_question_maximum_weight($index);

        if ($weight == $max_question_weight)
        {
            $html[] = '<td><span style="color:green"><b>' . $answer . '</b></span></td>';
        }
        elseif ($weight == 0)
        {
            $html[] = '<td><span style="color:red"><b>' . $answer . '</b></span></td>';
        }
        else
        {
            $html[] = '<td><span style="color:orange"><b>' . $answer . '</b></span></td>';
        }

        if ($this->get_assessment_result_processor()->get_assessment_viewer()->display_textual_feedback())
        {
            if ($weight != 0)
            {
                $as = $this->get_question()->get_answers($index);

                $html[] = '<td>';

                foreach ($as as $a)
                {
                    if ($a->get_value() == $answer && $a->get_position() == $index)
                    {
                        $html[] = $a->get_comment();
                    }
                }

                $html[] = '</td>';
            }
            else
            {
                $best_answer = $this->get_question()->get_best_answer_for_question($index);
                $html[] = '<td>';
                $html[] = Translation :: get('BestAnswerWas', array('ANSWER' => $best_answer->get_value()));
                if ($best_answer->has_comment())
                {
                    $html[] = '<br/>';
                    $html[] = $best_answer->get_comment();
                }
                $html[] = '</td>';
            }
        }

        if ($this->get_assessment_result_processor()->get_assessment_viewer()->display_numeric_feedback())
        {
            $html[] = '<td>' . $weight . ' / ' . $max_question_weight . '</td>';
        }

        $html[] = '</tr>';

        return implode("\n", $html);
    }

    function get_question_answer($index)
    {
        $result = array();
        $answers = $this->get_question()->get_answers();
        foreach ($answers as $answer)
        {
            if ($answer->get_position() == $index)
            {
                $result[] = $answer;
            }
        }

        return $result;
    }

    /**
     * @return boolean
     */
    function has_feedback()
    {
        if (! isset($this->has_feedback))
        {
            $answers = $this->get_answers();
            $this->has_feedback = false;

            foreach ($this->parts as $index => $part)
            {
                $weight = $this->get_question()->get_weight_from_answer($index, $answers[$index]);
                $max_question_weight = $this->get_question()->get_question_maximum_weight($index);

                if ($weight != 0)
                {
                    $as = $this->get_question()->get_answers($index);

                    foreach ($as as $a)
                    {
                        if ($a->get_value() == $answers[$index] && $a->get_position() == $index && $a->has_comment())
                        {
                            $this->has_feedback = true;
                        }
                    }
                }
                else
                {
                    $best_answer = $this->get_question()->get_best_answer_for_question($index);
                    if ($best_answer->has_comment())
                    {
                        $this->has_feedback = true;
                    }
                }

                $html[] = $this->get_question_feedback($index, $answers[$index]);
            }

        }

        return $this->has_feedback;
    }
}