<?php
/**
 * $Id: question_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc
 */
abstract class QuestionDisplay
{
    private $clo_question;
    private $question;
    private $question_nr;
    private $formvalidator;
    private $renderer;

    function QuestionDisplay($formvalidator, $clo_question, $question_nr, $question)
    {
        $this->formvalidator = $formvalidator;
        $this->renderer = $formvalidator->defaultRenderer();
        
        $this->clo_question = $clo_question;
        $this->question_nr = $question_nr;
        $this->question = $question;
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
        $html[] = $this->question->get_title();
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = '<div class="answer">';
        
        $description = $this->question->get_description();
        if ($this->question->has_description())
        {
            $html[] = '<div class="description">';
            $html[] = $description;
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

    static function factory($formvalidator, $clo_question, $question_nr)
    {
        $question = RepositoryDataManager :: get_instance()->retrieve_content_object($clo_question->get_ref());
        $type = $question->get_type();
        
        $file = dirname(__FILE__) . '/question_display/' . $type . '.class.php';
        
        if (! file_exists($file))
        {
            die('file does not exist: ' . $file);
        }
        
        require_once $file;
        
        $class = Utilities :: underscores_to_camelcase($type) . 'Display';
        $question_display = new $class($formvalidator, $clo_question, $question_nr, $question);
        return $question_display;
    }

    /**
     * @author Antonio Ognio
     * @source http://www.php.net/manual/en/function.shuffle.php (06-May-2008 04:42)
     */
    function shuffle_with_keys($array)
    {
        /* Auxiliary array to hold the new order */
        $aux = array();
        /* We work with an array of the keys */
        $keys = array_keys($array);
        /* We shuffle the keys */
        shuffle($keys);
        /* We iterate thru' the new order of the keys */
        foreach ($keys as $key)
        {
            /* We insert the key, value pair in its new order */
            $aux[$key] = $array[$key];
            /* We remove the element from the old array to save memory */
        }
        /* The auxiliary array with the new order overwrites the old variable */
        return $aux;
    }
}
?>