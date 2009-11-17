<?php
/**
 * $Id: ordering_question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.question_result_display
 */
require_once dirname(__FILE__) . '/../question_result_display.class.php';

class OrderingQuestionResultDisplay extends QuestionResultDisplay
{

    function display_question_result()
    {
        $html[] = '<table class="data_table take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th>' . Translation :: get('YourOrder') . '</th>';
        $html[] = '<th>' . Translation :: get('CorrectOrder') . '</th>';
        $html[] = '<th>' . Translation :: get('Answer') . '</th>';
        //$html[] = '<th>' . Translation :: get('Feedback') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        
        $answers = $this->get_question()->get_options();
        $user_answers = $this->get_answers();
        
        foreach ($answers as $i => $answer)
        {
            $html[] = '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td>' . $user_answers[$i + 1] . '</td>';
            $html[] = '<td>' . $answer->get_order() . '</td>';
            $html[] = '<td>' . $answer->get_value() . '</td>';
            //$html[] = '<td>' . Translation :: get('NoFeedback') . '</td>';
            $html[] = '</tr>';
        }
        
        $html[] = '</tbody>';
        $html[] = '</table>';
        
        echo implode("\n", $html);
    }
}
?>