<?php
/**
 * $Id: matching_question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.question_result_display
 */
require_once dirname(__FILE__) . '/../question_result_display.class.php';

class AssessmentMatchingQuestionResultDisplay extends QuestionResultDisplay
{

    function display_question_result()
    {
        $labels = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $html[] = '<table class="data_table take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="list"></th>';
        $html[] = '<th>' . Translation :: get('PossibleMatches') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';

        $label = 'A';
        $matches = $this->get_question()->get_matches();
        foreach ($matches as $i => $match)
        {
            $html[] = '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td>' . $label . '</td>';
            $html[] = '<td>' . $match . '</td>';
            $html[] = '</tr>';
            $label ++;
        }

        $html[] = '</tbody>';
        $html[] = '</table>';

        $html[] = '<table class="data_table take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th>' . Translation :: get('YourMatch') . '</th>';
        $html[] = '<th>' . Translation :: get('Correct') . '</th>';
        $html[] = '<th>' . Translation :: get('Option') . '</th>';
        $html[] = '<th>' . Translation :: get('Feedback') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';

        $answers = $this->get_answers();

        $options = $this->get_question()->get_options();
        foreach ($options as $i => $option)
        {
            $html[] = '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td>' . $labels[$answers[$i]] . '</td>';
            $html[] = '<td>' . $labels[$option->get_match()] . '</td>';
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