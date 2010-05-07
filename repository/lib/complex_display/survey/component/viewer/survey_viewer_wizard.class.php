<?php

require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';

require_once dirname(__FILE__) . '/wizard/survey_viewer_wizard_display.class.php';
require_once dirname(__FILE__) . '/wizard/survey_viewer_wizard_process.class.php';
require_once dirname(__FILE__) . '/wizard/survey_viewer_wizard_next.class.php';
require_once dirname(__FILE__) . '/wizard/survey_viewer_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/questions_survey_viewer_wizard_page.class.php';

class SurveyViewerWizard extends HTML_QuickForm_Controller
{
    
    private $parent;
    private $survey;
    private $template_id;
    private $total_pages;
    private $total_questions;
    private $pages;
    private $real_pages;

    function SurveyViewerWizard($parent, $survey, $template_id)
    {
        parent :: HTML_QuickForm_Controller('SurveyViewerWizard_' . $survey->get_id(), true);
        
        $this->parent = $parent;
        $this->survey = $survey;
        $this->template_id = $template_id;
                
        $this->add_pages();
        
        $this->addAction('next', new SurveyViewerWizardNext($this));
        $this->addAction('process', new SurveyViewerWizardProcess($this));
        $this->addAction('display', new SurveyViewerWizardDisplay($this));
    
    }

    function add_pages()
    {
        
        $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_SURVEY_ID, $this->survey->get_id());
        $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_TEMPLATE_ID, $this->template_id);
        $condition = new AndCondition($conditions);
    	$template_rel_pages = SurveyContextDataManager::get_instance()->retrieve_template_rel_pages($condition);
        $allowed_pages = array();
    	while ($template_rel_page = $template_rel_pages->next_result()) {
        	$allowed_pages[] = $template_rel_page->get_page_id();
        }
      	
    	$survey_pages = $this->survey->get_pages();
        $page_nr = 0;
        $question_nr = 0;
        
        $this->real_pages = array();
        
        foreach ($survey_pages as $survey_page)
        {
            if(! in_array($survey_page->get_id(), $allowed_pages)){
            	continue;
            }
        	
            
            
        	$page_nr ++;
        	$this->real_pages[$page_nr] = $survey_page->get_id();
            $this->addPage(new QuestionsSurveyViewerWizardPage('question_page_' . $page_nr, $this, $page_nr));
            $questions = array();
          
            
            $page_questions = $survey_page->get_questions();
            
            foreach ($page_questions as $question)
            {
                
            	if ($question->get_type() == SurveyDescription :: get_type_name())
                {
                    $questions[$question->get_id() . 'description'] = $question;
                }
                else
                {
                    $question_nr ++;
                    $questions[$question_nr] = $question;
                }
            
            }
            
            $this->pages[$page_nr] = array(page => $survey_page, questions => $questions);
        
        }
        
        if ($page_nr == 0)
        {
            $this->addPage(new QuestionsSurveyViewerWizardPage('question_page_' . $page_nr, $this, $page_nr));
        }
        
        $this->total_pages = $page_nr;
        $this->total_questions = $question_nr;
   
    }

    function get_questions($page_number)
    {
        $page = $this->pages[$page_number];
        $questions = $page['questions'];
        return $questions;
    }

    function get_page($page_number)
    {
        $page = $this->pages[$page_number];
        $page_object = $page['page'];
        return $page_object;
    }

    function get_parent()
    {
        return $this->parent;
    }

    function get_survey()
    {
        return $this->survey;
    }
	
    function get_real_page_id($page_nr){
      	return $this->real_pages[$page_nr];
    }
    
    function get_total_pages()
    {
        return $this->total_pages;
    }

    function get_total_questions()
    {
        return $this->total_questions;
    }

}
?>