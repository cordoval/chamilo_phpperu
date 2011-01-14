<?php
namespace repository\content_object\assessment;

use common\libraries\Translation;

/**
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.question_result_display
 */
require_once dirname(__FILE__) . '/../question_result_display.class.php';

class AssessmentMatchNumericQuestionResultDisplay extends QuestionResultDisplay
{

    function get_question_result()
    {
        $html = array();
        $html[] = '<div class="splitter">';
        $html[] = Translation :: get('YourAnswer');
        $html[] = '</div>';

        $html[] = '<div style="padding: 10px; border-left: 1px solid #B5CAE7; border-right: 1px solid #B5CAE7;">';
        $user_answer = $this->get_answers();

        if ($user_answer[0] && $user_answer[0] != '')
            $html[] = $user_answer[0];
        else
            $html[] = Translation :: get('NoAnswer');

        $html[] = '</div>';

        $html[] = '<table class="data_table take_assessment" style="border-top: 1px solid #B5CAE7;">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th>' . Translation :: get('PossibleAnswer') . '</th>';
        if ($this->get_assessment_result_processor()->get_assessment_viewer()->display_textual_feedback())
        {
            $html[] = '<th>' . Translation :: get('Feedback') . '</th>';
        }
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';

        $answers = $this->get_question()->get_options();

        foreach ($answers as $i => $answer)
        {
            $html[] = '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td>' . $answer->get_value() . '</td>';
            if ($this->get_assessment_result_processor()->get_assessment_viewer()->display_textual_feedback())
            {
                $html[] = '<td>' . $answer->get_feedback() . '</td>';
            }
            $html[] = '</tr>';
        }

        $html[] = '</tbody>';
        $html[] = '</table>';

        return implode("\n", $html);
    }
}
?>