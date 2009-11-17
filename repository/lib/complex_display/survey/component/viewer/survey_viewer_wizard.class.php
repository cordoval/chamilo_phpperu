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

    function SurveyViewerWizard($parent, $survey)
    {
        parent :: HTML_QuickForm_Controller('SurveyViewerWizard_' . $survey->get_id(), true);
        
        $this->parent = $parent;
        $this->survey = $survey;
        
        $this->addpages();
        
        $this->addAction('process', new SurveyViewerWizardProcess($this));
        $this->addAction('display', new SurveyViewerWizardDisplay($this));
    }

    function addpages()
    {
        $survey = $this->survey;
        $total_questions = RepositoryDataManager :: get_instance()->count_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $survey->get_id(), ComplexContentObjectItem :: get_table_name()));
        $questions_per_page = $survey->get_questions_per_page();
        
        if ($questions_per_page == 0)
        {
            $this->total_pages = 1;
        }
        else
        {
            $this->total_pages = ceil($total_questions / $questions_per_page);
        }
        
        for($i = 1; $i <= $this->total_pages; $i ++)
            $this->addPage(new QuestionsSurveyViewerWizardPage('question_page_' . $i, $this, $i));
        
        if (! isset($_SESSION['questions']))
            $_SESSION['questions'] = 'all';
    
    }

    function get_questions($page_number)
    {
        $survey = $this->survey;
        $questions_per_page = $this->survey->get_questions_per_page();
        
        if ($questions_per_page == 0)
        {
            $start = null;
            $stop = null;
        }
        else
        {
            $start = (($page_number - 1) * $questions_per_page);
            $stop = $questions_per_page;
        }
        $questions = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $survey->get_id(), ComplexContentObjectItem :: get_table_name()), array(), $start, $stop);
        return $questions;
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

}
?>