<?php
/**
 * $Id: fill_in_blanks_question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.question_result_display
 */
require_once dirname(__FILE__) . '/../question_result_display.class.php';

class FillInBlanksQuestionResultDisplay extends QuestionResultDisplay
{

    function display_question_result(){
        
        $answers = $this->get_answers();
    
        $answer_text = $this->get_question()->get_answer_text();
        $answer_text = nl2br($answer_text);
        $parts = preg_split(FillInBlanksQuestionAnswer::CLOZE_REGEX, $answer_text);
                
        $html[] = '<div class="with_borders">';
        $html[] = array_shift($parts);
        $index = 0;
        foreach($parts as $i => $part){
        	$answers[$i] = empty($answers[$i]) ? Translation :: get('NoAnswer') : $answers[$i];
        	if($this->get_question()->is_correct($i, $answers[$i])){
        		$html[] = '<span style="color:green"><b>'.$answers[$i].'</b></span>';
        	}else{
        		$html[] = '<span style="color:red"><b>'.$answers[$i].'</b></span>';
        	}
        	$html[] = $part;
        	$index++;
        }
        $html[] = '</div>';
        
        foreach($parts as $index => $part){
        	$html[] = $this->get_question_feedback($index);
        }
        echo implode("\n", $html);
    }
    
    function get_question_feedback($index){
    	$html[] = '<div class="with_borders"><b>'.Translation::get('Question').' '. ($index+1).'</b></div>';
        $html[] = '<table style="border-top: 1px solid #B5CAE7;" class="data_table take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="list checkbox">#</th>';
        $html[] = '<th class="list">' . Translation :: get('Answer') . '</th>';
        $html[] = '<th class="list">' . Translation :: get('Feedback') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        
        $i=0;
        $correct_answers = $this->get_question_answer($index);
        foreach ($correct_answers as $correct_answer){
        	$html[] = '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td>' . ($i + 1) . '</td>';
            $html[] = '<td>' . $correct_answer->get_value() . '</td>';
            $html[] = '<td>' . $correct_answer->get_comment() . '</td></tr>';
            $i++;
        }
        
        $html[] = '</tbody>';
        $html[] = '</table>';
        
        return implode("\n", $html);
    }
    
    function get_question_answer($index){
    	$result = array();
        $answers = $this->get_question()->get_answers();
        foreach($answers as $answer){
        	if($answer->get_position()==$index){
        		$result[] = $answer;
        	}
        }
        
        return $result;
    }
}