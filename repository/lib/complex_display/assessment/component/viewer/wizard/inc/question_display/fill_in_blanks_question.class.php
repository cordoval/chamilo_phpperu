<?php
/**
 * $Id: fill_in_blanks_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.question_display
 */
require_once dirname(__FILE__) . '/../question_display.class.php';

class FillInBlanksQuestionDisplay extends QuestionDisplay
{

    function add_question_form(){
        $clo_question = $this->get_clo_question();
        
        $question = $this->get_question();
        $answers = $question->get_answers();
        $question_type = $question->get_question_type();
        $answer_text = $question->get_answer_text();
        $answer_text = nl2br($answer_text);
        
        $parts = preg_split(FillInBlanksQuestionAnswer::CLOZE_REGEX, $answer_text);
        $this->add_html(array_shift($parts));
        $index = 0;
        
        $element_template = ' {element} ';
        $renderer = $this->get_renderer();
        $renderer->setElementTemplate($element_template, 'select');
        
        foreach($parts as $part){
            $name = $clo_question->get_id() . "[$index]";
            $this->add_question($name, $index, $question_type, $answers);
        	$this->add_html($part);
        	$index++;
        	$renderer->setElementTemplate($element_template, $name);
        }
    }
    
    function add_html($html){
    	$html = is_array($html) ? implode("\n", $html) : $html;
        $formvalidator = $this->get_formvalidator();
		$formvalidator->addElement('html', $html);
    }
    
    function add_select($name, $options){
        $formvalidator = $this->get_formvalidator();
        $formvalidator->addElement('select', $name, '', $options);
    }
    
    function add_text($name, $size){
        $formvalidator = $this->get_formvalidator();
        $formvalidator->addElement('text', $name, null, array('size'=>$size));
    }
    
    function add_question($name, $index, $question_type, $answers){
        $formvalidator = $this->get_formvalidator();
    	$options = $this->get_question_options($index, $answers);
        if($question_type == FillInBlanksQuestion :: TYPE_SELECT){
        	$this->add_select($name, $options);
        }else{
        	$size = 0;
        	foreach($options as $option){
        		$size = max($size, strlen($option));
        	}
        	$size = empty($size) ? 20 : $size;
        	$this->add_text($name, $size);
        }
    }

    function get_question_options($index, $answers){
        $result = array();
        foreach($answers as $answer){
        	if($answer->get_position()==$index){
            	$option = $answer->get_value();
            	$result[$option] = $option;
        	}
        }
        $this->shuffle_with_keys($result);
        return $result;
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
            $instruction[] = Translation :: get('FillInTheBlanks');
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