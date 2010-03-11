<?php
/**
 * $Id: survey_viewer_wizard.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey.component.viewer
 */
require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';

require_once dirname(__FILE__) . '/wizard/survey_viewer_wizard_display.class.php';
require_once dirname(__FILE__) . '/wizard/survey_viewer_wizard_process.class.php';
require_once dirname(__FILE__) . '/wizard/survey_viewer_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/questions_survey_viewer_wizard_page.class.php';

class SurveyViewerWizard extends HTML_QuickForm_Controller
{
    
    private $parent;
    private $survey;
    private $total_pages;
    private $total_questions;
    private $pages;
    private $visited_surveys;

    function SurveyViewerWizard($parent, $survey)
    {
        parent :: HTML_QuickForm_Controller('SurveyViewerWizard_' . $survey->get_id(), true);
        
        $this->parent = $parent;
        $this->survey = $survey;
               
        $this->add_pages();
      
        $this->addAction('process', new SurveyViewerWizardProcess($this));
        $this->addAction('display', new SurveyViewerWizardDisplay($this));
    
    }

    function add_pages()
    {
        
        $complex_content_objects = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->survey->get_id(), ComplexContentObjectItem :: get_table_name()));
        
        $this->total_pages = 0;
        
        while ($complex_content_object = $complex_content_objects->next_result())
        {
            $this->total_pages ++;
            $this->addPage(new QuestionsSurveyViewerWizardPage('question_page_' . $this->total_pages, $this, $this->total_pages));
            
            $survey_item = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_object->get_ref());
            $survey_page = RepositoryDataManager :: get_instance()->retrieve_content_object($survey_item->get_reference());
            $page_questions = $this->get_survey_page_questions($survey_page);
            $this->pages[$this->total_pages] = array(page => $survey_page, questions => $page_questions);
        }
        if($this->total_pages == 0){
        	$this->addPage(new QuestionsSurveyViewerWizardPage('question_page_' . $this->total_pages, $this, $this->total_pages));
        }
    	
    }

    function get_survey_page_questions($survey_page)
    {
        
        $complex_content_objects = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $survey_page->get_id(), ComplexContentObjectItem :: get_table_name()));
        $questions = array();
        
        while ($complex_content_object = $complex_content_objects->next_result())
        {
            $this->total_questions ++;
            $survey_page_item = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_object->get_ref());
            $questions[$this->total_questions] = RepositoryDataManager :: get_instance()->retrieve_content_object($survey_page_item->get_reference());
        }
        
        return $questions;
    
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

    function get_total_pages()
    {
        return $this->total_pages;
    }

    function get_surveys()
    {
        return $this->visited_surveys;
    }

    function get_total_questions()
    {
        return $this->total_questions;
    }

}
?>