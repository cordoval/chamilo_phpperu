<?php
/**
 * $Id: select_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey.component.viewer.wizard.inc.question_display
 */
require_once dirname(__FILE__) . '/../question_display.class.php';

class SelectQuestionDisplay extends QuestionDisplay
{

    function add_question_form()
    {
        $formvalidator = $this->get_formvalidator();
        $renderer = $this->get_renderer();
        $clo_question = $this->get_clo_question();
        $question = $this->get_question();
        
        $options = $question->get_options();
        $type = $question->get_answer_type();
        $question_id = $clo_question->get_id();
        
        foreach ($options as $option)
        {
            $answers[] = $option->get_value();
        }
        
        $element_template = array();
        $element_template[] = '<div><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '<div class="form_feedback"></div>';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '</div>';
        $element_template = implode("\n", $element_template);
        
        $question_name = $question_id . '_0';
        
        if ($type == 'checkbox')
        {
            $advanced_select = $formvalidator->createElement('advmultiselect', $question_name, '', $answers, array('style' => 'width: 200px;', 'class' => 'advanced_select_question'));
            $advanced_select->setButtonAttributes('add', 'class="add"');
            $advanced_select->setButtonAttributes('remove', 'class="remove"');
            $formvalidator->addElement($advanced_select);
        }
        else
        {
            $formvalidator->addElement('select', $question_name, '', $answers, 'class="select_question"');
        }
        
        $renderer->setElementTemplate($element_template, $question_name);
    }

    function add_borders()
    {
        return true;
    }

    function get_instruction()
    {
        $instruction = array();
        $question = $this->get_question();
        $type = $question->get_answer_type();
        
        if ($type == 'radio' && $question->has_description())
        {
            $instruction[] = '<div class="splitter">';
            $instruction[] = Translation :: get('SelectCorrectAnswer');
            $instruction[] = '</div>';
        }
        elseif ($type == 'checkbox' && $question->has_description())
        {
            $instruction[] = '<div class="splitter">';
            $instruction[] = Translation :: get('SelectCorrectAnswers');
            $instruction[] = '</div>';
        }
        else
        {
            $instruction = array();
        }
        
        return implode("\n", $instruction);
    }
}
?>