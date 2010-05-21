<?php
/**
 * $Id: assessment_select_question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.question_result_display
 */
require_once dirname(__FILE__) . '/../question_result_display.class.php';

class AssessmentSelectQuestionResultDisplay extends QuestionResultDisplay
{

    function display_question_result()
    {
        $html[] = '<table class="data_table take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="list">' . Translation :: get('Choice') . '</th>';
        $html[] = '<th class="list">' . Translation :: get('Correct') . '</th>';
        $html[] = '<th class="list">' . Translation :: get('Answer') . '</th>';
        $html[] = '<th class="list">' . Translation :: get('Feedback') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';

        $answers = $this->get_answers();

        $options = $this->get_question()->get_options();
        $type = $this->get_question()->get_answer_type();

        foreach ($options as $i => $option)
        {
            $html[] = '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">';

            if ($type == 'radio')
            {
                if ($answers[0] == $i)
                {
                    $selected = " checked ";
                }
                else
                {
                    $selected = "";
                }

                $html[] = '<td>' . '<input type="radio" name="yourchoice_' . $this->get_clo_question()->get_id() . '" value="' . $i . '" disabled' . $selected . '/>' . '</td>';
            }
            else
            {
                if (in_array($i, $answers[0]))
                {
                    $selected = " checked ";
                }
                else
                {
                    $selected = "";
                }

                $html[] = '<td>' . '<input type="checkbox" name="yourchoice' . $i . '" disabled' . $selected . '/>' . '</td>';
            }

            if ($option->is_correct())
            {
                $selected = " checked ";
            }
            else
            {
                $selected = "";
            }

            if ($type == 'radio')
            {
                $html[] = '<td>' . '<input type="radio" name="correctchoice_' . $this->get_clo_question()->get_id() . '" value="' . $i . '" disabled' . $selected . '/>' . '</td>';
            }
            else
            {
                $html[] = '<td>' . '<input type="checkbox" name="correctchoice_' . $i . '" disabled' . $selected . '/>' . '</td>';
            }

            $html[] = '<td>' . $option->get_value() . '</td>';
            $html[] = '<td>' . $option->get_feedback() . '</td>';
            $html[] = '</tr>';
        }

        $html[] = '</tbody>';
        $html[] = '</table>';

        echo implode("\n", $html);
    }
}
?>