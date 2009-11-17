<?php
/**
 * $Id: questions_survey_viewer_wizard_page.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey.component.viewer.wizard
 */
require_once dirname(__FILE__) . '/../../../../assessment/component/viewer/wizard/inc/question_display.class.php';

class QuestionsSurveyViewerWizardPage extends SurveyViewerWizardPage
{
    private $page_number;
    private $questions;

    function QuestionsSurveyViewerWizardPage($name, $parent, $number)
    {
        parent :: SurveyViewerWizardPage($name, $parent);
        $this->page_number = $number;
    }

    function get_number_of_questions()
    {
        return $this->questions->size();
    }

    function buildForm()
    {
        $this->_formBuilt = true;
        $this->questions = $this->get_parent()->get_questions($this->page_number);
        
        // Add buttons
        if ($this->page_number > 1)
            $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('back'), Translation :: get('Back'), array('class' => 'previous'));
        
        if ($this->page_number < $this->get_parent()->get_total_pages())
            $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Next'), array('class' => 'next'));
        else
            $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('submit'), Translation :: get('Submit'), array('class' => 'positive'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        // Add question forms
        $i = (($this->page_number - 1) * $this->get_parent()->get_survey()->get_questions_per_page()) + 1;
        
        while ($question = $this->questions->next_result())
        {
            $question_display = QuestionDisplay :: factory($this, $question, $i);
            $question_display->display();
            $i ++;
        }
        
        // Add buttons
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $renderer = $this->defaultRenderer();
        $renderer->setElementTemplate('<div style="float: right;">{element}</div><br /><br />', 'buttons');
        $renderer->setGroupElementTemplate('{element}', 'buttons');
        $this->setDefaultAction('next');
    }

    function get_page_number()
    {
        return $this->page_number;
    }
}
?>