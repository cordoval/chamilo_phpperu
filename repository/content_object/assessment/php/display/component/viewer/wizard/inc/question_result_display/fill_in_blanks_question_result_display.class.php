<?php
namespace repository\content_object\assessment;

use repository\content_object\fill_in_blanks_question\FillInBlanksQuestion;

use common\libraries\Translation;
use repository\content_object\fill_in_blanks_question\FillInBlanksQuestionAnswer;

/**
 * $Id: fill_in_blanks_question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.question_result_display
 */
require_once dirname(__FILE__) . '/../question_result_display.class.php';

class FillInBlanksQuestionResultDisplay extends QuestionResultDisplay
{

    function get_question_result()
    {
        $answers = $this->get_answers();

        $answer_text = $this->get_question()->get_answer_text();
        $answer_text = nl2br($answer_text);
        $parts = preg_split(FillInBlanksQuestionAnswer :: QUESTIONS_REGEX, $answer_text);

        $html[] = '<div class="with_borders">';
        $html[] = array_shift($parts);
        $index = 0;
        foreach ($parts as $i => $part)
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

        if ($this->get_assessment_result_processor()->get_assessment_viewer()->display_textual_feedback())
        {
            $html[] = '<div class="splitter"><b>' . Translation :: get('Questions') . '</b></div>';
            $html[] = '<table class="data_table take_assessment">';
            $html[] = '<thead>';
            $html[] = '<tr>';
            $html[] = '<th class="list checkbox">#</th>';
            $html[] = '<th class="list">#</th>';
            $html[] = '<th class="list">' . Translation :: get('Feedback') . '</th>';
            $html[] = '<th class="list">' . Translation :: get('Score') . '</th>';
            $html[] = '</tr>';
            $html[] = '</thead>';
            $html[] = '<tbody>';

            foreach ($parts as $index => $part)
            {
                $html[] = $this->get_question_feedback($index, $answers[$index], $weight, $max_question_weight);
            }

            $html[] = '</tbody>';
            $html[] = '</table>';
        }
        return implode("\n", $html);
    }

    function get_question_feedback($index, $answer, $weight, $max_question_weight)
    {
        //        $html = array();
        //        $html[] = '<div class="splitter"><b>' . Translation :: get('Question') . ' ' . ($index + 1) . '</b></div>';
        //        $html[] = '<table class="data_table take_assessment">';
        //        $html[] = '<thead>';
        //        $html[] = '<tr>';
        //        $html[] = '<th class="list checkbox">#</th>';
        //        $html[] = '<th class="list">' . Translation :: get('Answer') . '</th>';
        //        $html[] = '<th class="list">' . Translation :: get('Feedback') . '</th>';
        //        $html[] = '<th class="list">' . Translation :: get('Score') . '</th>';
        //        $html[] = '</tr>';
        //        $html[] = '</thead>';
        //        $html[] = '<tbody>';


        //        $i = 0;
        $correct_answers = $this->get_question_answer($index);
        $best_answer = $this->get_question()->get_best_answer_for_question($index);
        //        foreach ($correct_answers as $correct_answer)
        //        {
        $html[] = '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">';
        $html[] = '<td>' . ($index + 1) . '</td>';
        //            $html[] = '<td>' . $best_answer->get_value() . '</td>';
        $html[] = '<td>' . $best_answer->get_comment() . '</td>';
        $html[] = '<td>' . $best_answer->get_weight() . '</td>';
        $html[] = '</tr>';
        //            $i ++;
        //        }
        //
        //        $html[] = '</tbody>';
        //        $html[] = '</table>';


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
}