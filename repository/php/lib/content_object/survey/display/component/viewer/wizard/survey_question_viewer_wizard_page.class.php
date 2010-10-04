<?php

require_once dirname(__FILE__) . '/inc/survey_question_display.class.php';

class SurveyQuestionViewerWizardPage extends SurveyViewerWizardPage
{
    
    private $page_number;
    private $context_path;
    private $invitee_id;
    private $survey_page;
    
    /**
     * @var Survey
     */
    private $survey;

    function SurveyQuestionViewerWizardPage($name, $parent, $context_path, $survey_page_id, $page_number, $survey, $invitee_id)
    {
        parent :: SurveyViewerWizardPage($name, $parent);
        $this->context_path = $context_path;
        $this->survey_page = RepositoryDataManager::get_instance()->retrieve_content_object($survey_page_id);
        
        
        $this->page_number = $page_number;
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
            
            $question_display = SurveyQuestionDisplay :: factory($this, $complex_question, 1, $answer, $this->context_path);
            
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

    function get_survey_page()
    {
        return $this->survey_page;
    }
	
    function get_context_path(){
    	return $this->get_context_path();
    }
}

?>