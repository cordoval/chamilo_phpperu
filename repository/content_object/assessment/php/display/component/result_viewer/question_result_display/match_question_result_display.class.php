<?php
namespace repository\content_object\assessment;

use common\libraries\Translation;

/**
 * $Id: match_question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.result_viewer.question_result_display
 */
require_once dirname(__FILE__) . '/../question_result_display.class.php';

class MatchQuestionResultDisplay extends QuestionResultDisplay
{

    function display_question_result()
    {
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
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        
        $answers = $this->get_question()->get_options();
        
        foreach ($answers as $i => $answer)
        {
            $html[] = '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td>' . $answer->get_value() . '</td>';
            $html[] = '</tr>';
        }
        
        $html[] = '</tbody>';
        $html[] = '</table>';
        
        return implode("\n", $html);
    }
}
?>