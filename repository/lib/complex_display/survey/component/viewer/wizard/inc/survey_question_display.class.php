<?php
/**
 * $Id: survey_question_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey.component.viewer.wizard.inc
 */
abstract class SurveyQuestionDisplay
{
    private $clo_question;
    private $question;
    private $question_nr;
    private $formvalidator;
    private $renderer;
    private $survey;
    private $page_nr;

    function SurveyQuestionDisplay($formvalidator, $clo_question, $question_nr, $question, $survey, $page_nr)
    {
        $this->formvalidator = $formvalidator;
        $this->renderer = $formvalidator->defaultRenderer();
        
        $this->clo_question = $clo_question;
        $this->question_nr = $question_nr;
        $this->question = $question;
        $this->survey = $survey;
        $this->page_nr = $page_nr;
    }

    function get_clo_question()
    {
        return $this->clo_question;
    }

    function get_question()
    {
        return $this->question;
    }

    function get_renderer()
    {
        return $this->renderer;
    }

    function get_formvalidator()
    {
        return $this->formvalidator;
    }

    function get_survey()
    {
        return $this->survey;
    }

    function get_page_nr()
    {
        return $this->page_nr;
    }

    function display()
    {
        $formvalidator = $this->formvalidator;
        $this->add_header();
        if ($this->add_borders())
        {
            $header = array();
            $header[] = $this->get_instruction();
            $header[] = '<div class="with_borders">';
            
            $formvalidator->addElement('html', implode("\n", $header));
        }
        $this->add_question_form();
        if ($this->add_borders())
        {
            $footer = array();
            $footer[] = '<div class="clear"></div>';
            $footer[] = '</div>';
            $formvalidator->addElement('html', implode("\n", $footer));
        }
        $this->add_footer();
    }

    abstract function add_question_form();

    function add_header()
    {
        $formvalidator = $this->formvalidator;
        /*$clo_question = $this->get_clo_question();

		$number_of_questions = $formvalidator->get_number_of_questions();
		$current_question = $this->question_nr;*/
        
        $html[] = '<div class="question">';
        $html[] = '<div class="title">';
        $html[] = '<div class="number">';
        $html[] = '<div class="bevel">';
        $html[] = $this->question_nr . '.';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="text">';
        $html[] = '<div class="bevel">';
        //$html[] = '<img src="'. Theme :: get_common_image_path(). 'treemenu_types/' .$this->question->get_icon_name().'.png" />';
        $title = $this->question->get_title();
        $html[] = $this->parse($title);
        
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = '<div class="answer">';
        
        $description = $this->question->get_description();
        if ($this->question->has_description())
        {
            $html[] = '<div class="description">';
            
            $html[] = $this->parse($description);
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div>';
        }
        
        $html[] = '<div class="clear"></div>';
        
        $header = implode("\n", $html);
        $formvalidator->addElement('html', $header);
    }

    function add_footer($formvalidator)
    {
        $formvalidator = $this->formvalidator;
        
        $html[] = '</div>';
        $html[] = '</div>';
        
        $footer = implode("\n", $html);
        $formvalidator->addElement('html', $footer);
    }

    function add_borders()
    {
        return false;
    }

    abstract function get_instruction();

    static function factory($formvalidator, $clo_question, $question_nr, $survey, $page_nr)
    {
        $question = $clo_question;
        $type = $question->get_type();
        
        $file = dirname(__FILE__) . '/survey_question_display/' . $type . '.class.php';
        
        if (! file_exists($file))
        {
            die('file does not exist: ' . $file);
        }
        
        require_once $file;
        
        $class = Utilities :: underscores_to_camelcase($type) . 'Display';
        $question_display = new $class($formvalidator, $clo_question, $question_nr, $question, $survey, $page_nr);
        return $question_display;
    }

    function parse($value)
    {
        $context = $this->survey->get_context_instance();
        $explode = explode('$V{', $value);
        
        $new_value = array();
        foreach ($explode as $part)
        {
            
            $vars = explode('}', $part);
            
            if (count($vars) == 1)
            {
                $new_value[] = $vars[0];
            }
            else
            {
                $var = $vars[0];
                
                $replace = $context->get_additional_property($var);
                
                $new_value[] = $replace . ' ' . $vars[1];
            }
        
        }
        return implode(' ', $new_value);
    }

}
?>