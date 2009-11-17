<?php
/**
 * $Id: match_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey.component.viewer.wizard.inc.question_display
 */
require_once dirname(__FILE__) . '/../question_display.class.php';

class MatchQuestionDisplay extends QuestionDisplay
{

    function add_question_form()
    {
        $formvalidator = $this->get_formvalidator();
        $renderer = $this->get_renderer();
        $clo_question = $this->get_clo_question();
        $question = $this->get_question();
        
        $textarea_width = '400px';
        $textarea_height = '50px';
        $textarea_style = 'width: ' . $textarea_width . '; height: ' . $textarea_height . ';';
        
        $element_template = array();
        $element_template[] = '<div><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '<div class="form_feedback"></div>';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '</div>';
        $element_template = implode("\n", $element_template);
        
        $name = $clo_question->get_id() . '_0';
        $formvalidator->addElement('textarea', $name, '', array('style' => $textarea_style));
        $renderer->setElementTemplate($element_template, $name);
    }

    function add_borders()
    {
        return true;
    }

    function get_instruction()
    {
        $instruction = array();
        $question = $this->get_question();
        
        if ($question->has_description())
        {
            $instruction[] = '<div class="splitter">';
            $instruction[] = Translation :: get('EnterAnswer');
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