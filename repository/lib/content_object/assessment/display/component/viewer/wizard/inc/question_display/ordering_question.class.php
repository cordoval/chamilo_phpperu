<?php
/**
 * $Id: ordering_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.question_display
 */
require_once dirname(__FILE__) . '/../question_display.class.php';

class OrderingQuestionDisplay extends QuestionDisplay
{

    function add_question_form()
    {
        $formvalidator = $this->get_formvalidator();
        $renderer = $this->get_renderer();
        $clo_question = $this->get_clo_question();
        $question = $this->get_question();
        $answers = $this->shuffle_with_keys($question->get_options());
        
        $table_header = array();
        $table_header[] = '<table class="data_table take_assessment">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="checkbox"></th>';
        $table_header[] = '<th>' . $this->get_instruction() . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formvalidator->addElement('html', implode("\n", $table_header));
        
        $question_id = $clo_question->get_id();
        $order_options = $this->get_order_options();
        
        foreach ($answers as $i => $answer)
        {
            $group = array();
            $answer_name = $question_id . '_' . ($i + 1);
            $group[] = $formvalidator->createElement('select', $answer_name, null, $order_options);
            $group[] = $formvalidator->createElement('static', null, null, $answer->get_value());
            
            $formvalidator->addGroup($group, 'option_' . $i, null, '', false);
            
            $renderer->setElementTemplate('<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 'option_' . $i);
            $renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $i);
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
        if ($this->get_question()->has_description())
        {
            $title = Translation :: get('PutAnswersCorrectOrder');
        }
        else
        {
            $title = '';
        }
        
        return $title;
    }

    function get_order_options()
    {
        $answer_count = count($this->get_question()->get_options());
        
        $options = array();
        for($i = 1; $i <= $answer_count; $i ++)
        {
            $options[$i] = $i;
        }
        
        return $options;
    }
}
?>