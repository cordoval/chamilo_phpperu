<?php namespace repository\content_object\survey;
/**
 * $Id: survey_select_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey.component.viewer.wizard.inc.question_display
 */
require_once dirname(__FILE__) . '/../survey_question_display.class.php';

class SurveySelectQuestionDisplay extends SurveyQuestionDisplay
{

    function add_question_form()
    {
        $formvalidator = $this->get_formvalidator();
        $renderer = $this->get_renderer();
        $complex_question = $this->get_complex_question();
        $question = $this->get_question();
        
        $answer = $this->get_answer();
            
        $options = $question->get_options();
        $type = $question->get_answer_type();
        $question_id = $question->get_id();
        
        foreach ($options as $option)
        {
            $answer_options[] = $option->get_value();
        }
        
        $element_template = array();
        $element_template[] = '<div><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '<div class="form_feedback"></div>';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '</div>';
        $element_template = implode("\n", $element_template);
        
        $question_name = $complex_question->get_id().'_0';
        
        if ($type == 'checkbox')
        {
            $advanced_select = $formvalidator->createElement('advmultiselect', $question_name, '', $answer_options, array('style' => 'width: 200px;', 'class' => 'advanced_select_question'));
            $advanced_select->setButtonAttributes('add', 'class="add"');
            $advanced_select->setButtonAttributes('remove', 'class="remove"');
            $formvalidator->addElement($advanced_select);
            if($answer){
            	$formvalidator->setDefaults(array($question_name =>array_values($answer[0])));
            }
        }
        else
        {
            $select_box = $formvalidator->createElement('select', $question_name, '', $answer_options, 'class="select_question"');
        	$formvalidator->addElement($select_box);
        	if($answer){
        		$formvalidator->setDefaults(array($question_name =>$answer[0]));
        	}
        	
//            $formvalidator->addElement('select', $question_name, '', $answer_options, 'class="select_question"');
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
            $instruction[] = Translation :: get('SelectYourChoice');
            $instruction[] = '</div>';
        }
        elseif ($type == 'checkbox' && $question->has_description())
        {
            $instruction[] = '<div class="splitter">';
            $instruction[] = Translation :: get('SelectYourChoices');
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