<?php
/**
 * $Id: survey_matrix_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey.component.viewer.wizard.inc.question_display
 */
require_once dirname(__FILE__) . '/../survey_question_display.class.php';

class SurveyMatrixQuestionDisplay extends SurveyQuestionDisplay
{

    function add_question_form()
    {
        $formvalidator = $this->get_formvalidator();
        $renderer = $this->get_renderer();
        $complex_question = $this->get_complex_question();
        $question = $this->get_question();
        
        $options = $question->get_options();
        $matches = $question->get_matches();
        $type = $question->get_matrix_type();
        
        $table_header = array();
        $table_header[] = '<table class="data_table take_assessment">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="caption" style="width: 30%;"></th>';
        
        foreach ($matches as $match)
        {
            $table_header[] = '<th class="center">' . strip_tags($match) . '</th>';
        }
        
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formvalidator->addElement('html', implode("\n", $table_header));
        
        $question_id = $complex_question->get_id();
        
        $answer = $this->get_answer();
               
        foreach ($options as $i => $option)
        {
            $group = array();
            
            $group[] = $formvalidator->createElement('static', null, null, '<div style="text-align: left;">' . $option->get_value() . '</div>');
            
            foreach ($matches as $j => $match)
            {
                if ($type == MatrixQuestion :: MATRIX_TYPE_RADIO)
                {
                    $option_name = $question_id . '_' . $i . '_0';
                    
                    $radio = $formvalidator->createElement('radio', $option_name, null, null, $j);
                    if ($answer)
                    {
                        if ($answer[$i] == $j)
                        {
                            $formvalidator->setDefaults(array($option_name => $j));
                        }
                    }
                    
                    $group[] = $radio;
                }
                elseif ($type == MatrixQuestion :: MATRIX_TYPE_CHECKBOX)
                {
                    $option_name = $question_id . '_' . $i . '_' . $j;
                    
                    $checkbox = $formvalidator->createElement('checkbox', $option_name, '', '');
                    if ($answer[$i][$j] == 1)
                    {
                        $formvalidator->setDefaults(array($option_name => 1));
                    }
                    
                    $group[] = $checkbox;
                }
            }
            
            $formvalidator->addGroup($group, 'matrix_option_' . $i, null, '', false);
            
            $renderer->setElementTemplate('<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 'matrix_option_' . $i);
            $renderer->setGroupElementTemplate('<td style="text-align: center;">{element}</td>', 'matrix_option_' . $i);
        }
        
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formvalidator->addElement('html', implode("\n", $table_footer));
    
    }

    function add_border()
    {
        return false;
    }

    function get_instruction()
    {
    
    }
}
?>