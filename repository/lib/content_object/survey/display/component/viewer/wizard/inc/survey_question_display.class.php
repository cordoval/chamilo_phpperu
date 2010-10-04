<?php

abstract class SurveyQuestionDisplay
{
    private $complex_question;
    private $question;
    private $question_nr;
    private $formvalidator;
    private $renderer;
    
    /**
     * @var Survey
     */
    private $survey;
    private $page_nr;
    private $answer;
    private $visible;
    private $contex_path;

    function SurveyQuestionDisplay($formvalidator, $complex_question, $question, $answer, $context_path, $survey)
    {
        $this->formvalidator = $formvalidator;
        $this->renderer = $formvalidator->defaultRenderer();
        $this->complex_question = $complex_question;
        
        $this->question = $question;
        $this->answer = $answer;
        $this->contex_path = $context_path;
        $this->survey = $survey;
//        dump($context_path);
        $this->question_nr = $this->survey->get_question_nr($context_path.'_'.$complex_question->get_id());;
    }

    function get_complex_question()
    {
        return $this->complex_question;
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

    function get_answer()
    {
        return $this->answer;
    }

    function get_context_path()
    {
        return $this->contex_path;
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
        
        if (! $this->get_complex_question()->is_visible())
        {
            $html[] = '<div style="display:none" class="question" id="survey_question_' . $this->question->get_id() . '">';
        
        }
        else
        {
            $html[] = '<div  class="question" id="survey_question_' . $this->question->get_id() . '">';
        
        }
        
        $html[] = '<div class="title">';
        $html[] = '<div class="number">';
        $html[] = '<div class="bevel">';
        $html[] = $this->question_nr . '.';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="text">';
        $html[] = '<div class="bevel">';
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

    static function factory($formvalidator, $complex_question, $answer, $context_path, $survey)
    {
        
        $question = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_question->get_ref());
        
        $type = $question->get_type();
        
        $file = dirname(__FILE__) . '/survey_question_display/' . $type . '.class.php';
        
        if (! file_exists($file))
        {
            die('file does not exist: ' . $file);
        }
        
        require_once $file;
        
        $class = Utilities :: underscores_to_camelcase($type) . 'Display';
        $question_display = new $class($formvalidator, $complex_question, $question, $answer, $context_path, $survey);
        return $question_display;
    }

    function parse($value)
    {
        return $this->survey->parse($this->context_path, $value);
    }

}
?>