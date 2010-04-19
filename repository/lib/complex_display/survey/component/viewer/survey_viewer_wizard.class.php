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
    private $total_pages;
    private $total_questions;
    private $pages;

    function SurveyViewerWizard($parent, $survey)
    {
        parent :: HTML_QuickForm_Controller('SurveyViewerWizard_' . $survey->get_id(), true);
        
        $this->parent = $parent;
        $this->survey = $survey;
        
        $this->add_pages();
        
        $this->addAction('next', new SurveyViewerWizardNext($this));
        $this->addAction('process', new SurveyViewerWizardProcess($this));
        $this->addAction('display', new SurveyViewerWizardDisplay($this));
    
    }

    function add_pages()
    {
        
        $survey_pages = $this->survey->get_pages();
        $page_nr = 0;
        $question_nr = 0;
        
        while ($survey_page = $survey_pages->next_result())
        {
            
            $page_nr ++;
            $this->addPage(new QuestionsSurveyViewerWizardPage('question_page_' . $page_nr, $this, $page_nr));
            $questions = array();
            $page_questions = $survey_page->get_questions();
            while ($question = $page_questions->next_result())
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
        
    //        $complex_content_objects = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->survey->get_id(), ComplexContentObjectItem :: get_table_name()));
    //        
    //        $this->total_pages = 0;
    //        
    //        while ($complex_content_object = $complex_content_objects->next_result())
    //        {
    //            $this->total_pages ++;
    //            $this->addPage(new QuestionsSurveyViewerWizardPage('question_page_' . $this->total_pages, $this, $this->total_pages));
    //            
    //            $survey_page = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_object->get_ref());
    //            $page_questions = $this->get_survey_page_questions($survey_page);
    //            $this->pages[$this->total_pages] = array(page => $survey_page, questions => $page_questions);
    //        }
    //        if ($this->total_pages == 0)
    //        {
    //            $this->addPage(new QuestionsSurveyViewerWizardPage('question_page_' . $this->total_pages, $this, $this->total_pages));
    //        }
    

    }

    //    function get_survey_page_questions($survey_page)
    //    {
    //        
    //        $complex_content_objects = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $survey_page->get_id(), ComplexContentObjectItem :: get_table_name()));
    //        $questions = array();
    //        
    //        while ($complex_content_object = $complex_content_objects->next_result())
    //        {
    //            
    //            $question = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_object->get_ref());
    //            
    //            if ($question->get_type() == SurveyDescription :: get_type_name())
    //            {
    //                $questions[$question->get_id() . 'description'] = $question;
    //            }
    //            else
    //            {
    //                $this->total_questions ++;
    //                $questions[$this->total_questions] = $question;
    //            }
    //        
    //        }
    //        
    //        return $questions;
    //    
    //    }
    

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

    function get_total_questions()
    {
        return $this->total_questions;
    }

}
?>