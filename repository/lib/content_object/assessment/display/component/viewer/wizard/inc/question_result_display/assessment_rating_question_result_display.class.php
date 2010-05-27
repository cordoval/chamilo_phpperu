<?php
/**
 * $Id: assessment_rating_question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.question_result_display
 */
require_once dirname(__FILE__) . '/../question_result_display.class.php';

class AssessmentRatingQuestionResultDisplay extends QuestionResultDisplay
{

    function display_question_result()
    {
        $html[] = '<table class="data_table take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="list">' . Translation :: get('YourValue') . '</th>';
        $html[] = '<th class="list">' . Translation :: get('CorrectValue') . '</th>';
        //$html[] = '<th class="list">' . Translation :: get('Feedback') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';

        $answers = $this->get_answers();

        $html[] = '<tr>';
        $html[] = '<td>' . $answers[0] . '</td>';
        $html[] = '<td>' . $this->get_question()->get_correct() . '</td>';
        //$html[] = '<td></td>';
        $html[] = '</tr>';

        $html[] = '</tbody>';
        $html[] = '</table>';

        echo implode("\n", $html);
    }
}
?>