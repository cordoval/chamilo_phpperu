<?php
/**
 * $Id: rating_question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.result_viewer.question_result_display
 */
require_once dirname(__FILE__) . '/../question_result_display.class.php';

class RatingQuestionResultDisplay extends QuestionResultDisplay
{

    function display_question_result()
    {
        $html[] = '<table class="data_table take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="list">' . Translation :: get('YourValue') . '</th>';
        $html[] = '<th class="list">' . Translation :: get('CorrectValue') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        
        $answers = $this->get_answers();
        
        $html[] = '<tr>';
        $html[] = '<td>' . $answers[0] . '</td>';
        $html[] = '<td>' . $this->get_question()->get_correct() . '</td>';
        $html[] = '</tr>';
        
        $html[] = '</tbody>';
        $html[] = '</table>';
        
        return implode("\n", $html);
    }
}
?>