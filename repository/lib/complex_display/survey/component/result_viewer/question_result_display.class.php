<?php
/**
 * $Id: question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey.component.result_viewer
 */
abstract class QuestionResultDisplay
{
    private $clo_question;
    private $question;
    private $question_nr;
    private $answers;
    private $score;
    private $feedback;
    private $form;
    private $can_change;

    function QuestionResultDisplay(&$form, $clo_question, $question_nr, $answers, $score, $feedback, $can_change)
    {
        $this->clo_question = $clo_question;
        $this->question_nr = $question_nr;
        $this->question = $clo_question->get_ref();
        $this->answers = $answers;
        $this->score = $score;
        $this->feedback = $feedback;
        $this->form = $form;
        $this->can_change = $can_change;
    }

    function get_clo_question()
    {
        return $this->clo_question;
    }

    function get_question()
    {
        return $this->question;
    }

    function get_question_nr()
    {
        return $this->question_nr;
    }

    function get_answers()
    {
        return $this->answers;
    }

    function get_score()
    {
        return $this->score;
    }

    function get_feedback()
    {
        return $this->feedback;
    }

    function display()
    {
        $this->display_header();
        
        if ($this->add_borders())
        {
            $header = array();
            $header[] = '<div class="with_borders">';
            
            $this->form->addElement('html', implode("\n", $header));
        }
        
        $this->form->addElement('html', $this->display_question_result());
        
        if ($this->add_borders())
        {
            $footer = array();
            $footer[] = '<div class="clear"></div>';
            $footer[] = '</div>';
            $this->form->addElement('html', implode("\n", $footer));
        }
        
        $this->form->addElement('html', $this->display_footer());
    }

    function display_question_result()
    {
        return $this->get_score() . '<br />';
    }

    function display_header()
    {
        $html = array();
        
        $html[] = '<div class="question">';
        $html[] = '<div class="title">';
        $html[] = '<div class="number">';
        $html[] = '<div class="bevel">';
        $html[] = $this->question_nr . '.';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="text">';
        
        $html[] = '<div class="bevel">';
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
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
        }
        
        $html[] = '<div class="clear"></div>';
        
        $this->form->addElement('html', implode("\n", $html));
    }

    function display_footer()
    {
        $html[] = '</div>';
        $html[] = '</div>';
        
        $footer = implode("\n", $html);
        return $footer;
    }

    function add_borders()
    {
        return false;
    }

    static function factory(&$form, $clo_question, $question_nr, $answers, $score, $feedback, $can_change)
    {
        $type = $clo_question->get_ref()->get_type();
        
        $file = dirname(__FILE__) . '/survey_question_result_display/' . $type . '_result_display.class.php';
        
        if (! file_exists($file))
        {
            die('file does not exist: ' . $file);
        }
        
        require_once $file;
        
        $class = Utilities :: underscores_to_camelcase($type) . 'ResultDisplay';
        $question_result_display = new $class($form, $clo_question, $question_nr, $answers, $score, $feedback, $can_change);
        return $question_result_display;
    }
}
?>