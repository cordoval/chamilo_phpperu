<?php

require_once dirname(__FILE__) . '/inc/survey_question_display.class.php';

class SurveyQuestionViewerWizardPage extends SurveyViewerWizardPage
{
    
    private $page_nr;
    private $context_path;
    private $invitee_id;
    
    /**
     * @var Survey
     */
    private $survey;

    function SurveyQuestionViewerWizardPage($name, $parent, $context_path, $page_nr, $survey, $invitee_id)
    {
        parent :: SurveyViewerWizardPage($name, $parent);
        $this->context_path = $context_path;
        $this->page_nr = $page_nr;
        $this->survey = $survey;
        $this->invitee_id = $invitee_id;
    }

    function buildForm()
    {
        $this->_formBuilt = true;
        
        // Add buttons
        if ($this->page_nr > 1)
        {
            $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('back'), Translation :: get('Back'), array('class' => 'previous'));
        }
        
        if ($this->page_nr < $this->get_parent()->get_total_pages())
        {
            $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Next'), array('class' => 'next'));
        
        }
        else
        {
            $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('submit'), Translation :: get('Submit'), array('class' => 'positive'));
        }
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        // Add question forms
        

        $complex_questions = $this->survey->get_page_complex_questions($this->invitee_id, $this->context_path);
        
        foreach ($complex_questions as $complex_question)
        {
            
            $answer = $this->get_parent()->get_answer($complex_question->get_id, $this->context_path);
            
            $question_display = SurveyQuestionDisplay :: factory($this, $complex_question, 1, $answer);
            
            $question_display->display();
        }
        
        // Add buttons second time
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

    function get_real_page_number()
    {
        $this->get_parent()->get_real_page_nr($this->page_number);
    }

}

?>