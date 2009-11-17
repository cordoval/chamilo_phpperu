<?php
/**
 * $Id: matrix_question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey.component.result_viewer.survey_question_result_display
 */
require_once dirname(__FILE__) . '/../question_result_display.class.php';

class MatrixQuestionResultDisplay extends QuestionResultDisplay
{

    function display_question_result()
    {
        $answers = $this->get_answers();
        $options = $this->get_question()->get_options();
        $matches = $this->get_question()->get_matches();
        $type = $this->get_question()->get_matrix_type();
        
        $html[] = '<table class="data_table take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th></th>';
        
        foreach ($matches as $match)
        {
            $html[] = '<th>' . $match . '</th>';
        }
        
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        
        foreach ($options as $i => $option)
        {
            $html[] = '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td>' . $option->get_value() . '</td>';
            
            foreach ($matches as $j => $match)
            {
                $html[] = '<td>';
                if ($type == MatrixQuestion :: MATRIX_TYPE_RADIO)
                {
                    if ($answers[$i] == $j)
                    {
                        $selected = " checked ";
                        
                        if ($option->get_matches() == $j)
                        {
                            $result = Theme :: get_common_image('action_confirm');
                        }
                        else
                        {
                            $result = Theme :: get_common_image('action_delete');
                        }
                    }
                    else
                    {
                        $selected = '';
                        if ($option->get_matches() == $j)
                        {
                            $result = Theme :: get_common_image('action_metadata');
                        }
                        else
                        {
                            $result = '';
                        }
                    }
                    
                    $html[] = '<input type="radio" name="yourchoice_' . $this->get_clo_question()->get_id() . '_' . $i . '" value="' . $j . '" disabled' . $selected . '/>';
                    $html[] = $result;
                }
                else
                {
                    if (array_key_exists($j, $answers[$i]))
                    {
                        $selected = " checked ";
                        
                        if (in_array($j, $option->get_matches()))
                        {
                            $result = Theme :: get_common_image('action_confirm');
                        }
                        else
                        {
                            $result = Theme :: get_common_image('action_delete');
                        }
                    }
                    else
                    {
                        $selected = '';
                        
                        if (in_array($j, $option->get_matches()))
                        {
                            $result = Theme :: get_common_image('action_metadata');
                        }
                        else
                        {
                            $result = '';
                        }
                    }
                    
                    $html[] = '<input type="checkbox" name="yourchoice_' . $i . '_' . $j . '" disabled' . $selected . '/>';
                    $html[] = $result;
                
                }
                
                $html[] = '</td>';
            }
            
            $html[] = '</tr>';
        }
        
        $html[] = '</tbody>';
        $html[] = '</table>';
        
        return implode("\n", $html);
    }
}
?>