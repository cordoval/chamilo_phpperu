<?php

require_once dirname(__FILE__) . '/inc/survey_question_display.class.php';

class SurveyQuestionViewerWizardPage extends SurveyViewerWizardPage
{
    
    private $page_number;
    private $context_path;
    private $survey_page;
    
    /**
     * @var Survey
     */
    private $survey;

    function SurveyQuestionViewerWizardPage($name, $parent, $context_path, $survey)
    {
        parent :: SurveyViewerWizardPage($name, $parent);
        $this->context_path = $context_path;
        
        $this->survey = $survey;
//        dump($this->context_path);
        $this->page_number = $this->survey->get_page_nr($this->context_path);
        $this->survey_page = $this->survey->get_survey_page($this->context_path);
    }

    function buildForm()
    {
        $this->_formBuilt = true;
        
        //        dump('in form');
        

        // Add buttons
        if ($this->page_number > 1)
        {
            $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('back'), Translation :: get('Back'), array('class' => 'previous'));
        }
        
        if ($this->page_number < $this->survey->count_pages())
        {
            $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Next'), array('class' => 'next'));
        
        }
        else
        {
            $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('submit'), Translation :: get('Submit'), array('class' => 'positive'));
        }
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        // Add question forms
        

        $complex_questions = $this->survey->get_page_complex_questions($this->context_path);
        
        foreach ($complex_questions as $complex_question)
        {
            
           	$question_context_path = $this->context_path.'_'.$complex_question->get_id();
                      	
           	$answer = $this->get_parent()->get_answer($complex_question->get_id(), $question_context_path);
                       
            $question_display = SurveyQuestionDisplay :: factory($this, $complex_question, $answer, $question_context_path, $this->survey);
            
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

    function get_context_path()
    {
        return $this->context_path;
    }
    
    function get_question_context_paths(){
    	return $this->survey->get_page_question_context_paths($this->get_context_path());
    }
    
}

?>