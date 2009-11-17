<?php
/**
 * $Id: fill_in_blanks_question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey.component.result_viewer.survey_question_result_display
 */
require_once dirname(__FILE__) . '/../question_result_display.class.php';

class FillInBlanksQuestionResultDisplay extends QuestionResultDisplay
{

    function display_question_result()
    {
        $answer_text = $this->get_question()->get_answer_text();
        $answer_text = nl2br($answer_text);
        
        $matches = array();
        preg_match_all('/\[[a-zA-Z0-9_êëûüôöîïéèà\s\-]*\]/', $answer_text, $matches);
        $matches = $matches[0];
        $answers = $this->get_answers();
        
        foreach ($matches as $i => $match)
        {
            $correct_answer = substr($match, 1, strlen($match) - 2);
            
            if ($answers[$i] == null)
            {
                $answers[$i] = Translation :: get('NoAnswer');
            }
            
            if ($correct_answer == $answers[$i])
            {
                $my_answer = '<span style="color: green;" >' . $answers[$i] . '</span>';
            }
            else
            {
                $my_answer = '<span style="color: red;" >' . $answers[$i] . '</span>';
            }
            
            $answer_text = str_replace($match, $my_answer . ' / <span style="color: blue" >' . $correct_answer . '</span>', $answer_text);
        }
        
        $html[] = '<div style="border-left: 1px solid #B5CAE7; border-right: 1px solid#B5CAE7; padding: 10px;">';
        $html[] = $answer_text . '</div>';
        
        return implode("\n", $html);
    }

}
?>