<?php
/**
 * $Id: survey_rating_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey.component.viewer.wizard.inc.question_display
 */
require_once dirname(__FILE__) . '/../survey_question_display.class.php';

class SurveyRatingQuestionDisplay extends SurveyQuestionDisplay
{

    function add_question_form()
    {
        $formvalidator = $this->get_formvalidator();
        $renderer = $this->get_renderer();
//        $complex_question = $this->get_complex_question();
        $question = $this->get_question();
        
        $min = $question->get_low();
        $max = $question->get_high();
        $question_name = $this->get_complex_question()->get_id() . '_0';
        
//        $question_name = $this->get_question()->get_id() . '_0'.'_'.$this->get_context_path();
        
        for($i = $min; $i <= $max; $i ++)
        {
            $scores[$i] = $i;
        }
        
        $element_template = array();
        $element_template[] = '<div><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '<div class="form_feedback"></div>';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '</div>';
        $element_template = implode("\n", $element_template);
        
        $formvalidator->addElement('select', $question_name, Translation :: get('Rating') . ': ', $scores, 'class="rating_slider"');
        $renderer->setElementTemplate($element_template, $question_name);
        $formvalidator->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/rating_question.js'));
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
            $instruction[] = Translation :: get('ChooseYourRating');
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