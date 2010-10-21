<?php

require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';

require_once dirname(__FILE__) . '/wizard/survey_viewer_wizard_display.class.php';
require_once dirname(__FILE__) . '/wizard/survey_viewer_wizard_process.class.php';
require_once dirname(__FILE__) . '/wizard/survey_viewer_wizard_next.class.php';
require_once dirname(__FILE__) . '/wizard/survey_viewer_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/survey_question_viewer_wizard_page.class.php';

class SurveyViewerWizard extends HTML_QuickForm_Controller
{
    
    const PARAM_SURVEY_ID = 'survey_id';
    const PARAM_PUBLICATION_ID = 'publication_id';
    const PARAM_CONTEXT_TEMPLATE_ID = 'context_template_id';
    const PARAM_TEMPLATE_ID = 'template_id';
    const PARAM_CONTEXT_ID = 'context_id';
    const PARAM_INVITEE_ID = 'invitee_id';
    const PARAM_CONTEXT_PATH = 'path';
  
    private $parent;
    /**
     * @var Survey
     */
    private $survey;

    function SurveyViewerWizard($parent)
    {
        $this->parent = $parent;
        $survey_id = Request :: get(self :: PARAM_SURVEY_ID);
        
        parent :: HTML_QuickForm_Controller('SurveyViewerWizard_' . $survey_id, true);
        
        $invitee_id = Request :: get(self :: PARAM_INVITEE_ID);
        $this->survey = RepositoryDataManager :: get_instance()->retrieve_content_object($survey_id);
        
        $this->survey->initialize($invitee_id);
      
        
        $this->add_pages();
        $this->addAction('next', new SurveyViewerWizardNext($this));
        $this->addAction('process', new SurveyViewerWizardProcess($this));
        $this->addAction('display', new SurveyViewerWizardDisplay($this));
        
        $this->started();
    
    }

    function add_pages()
    {
        
        $page_context_paths = $this->survey->get_page_context_paths();
        
//        dump($page_context_paths);
//        dump(count($page_context_paths));
//        dump($this->survey->get_context_paths());
//        dump(count($this->survey->get_context_paths()));
        
        if (count($page_context_paths))
        {
            foreach ($page_context_paths as $page_context_path)
            {
                   $this->addPage(new SurveyQuestionViewerWizardPage('page_' . $page_context_path, $this, $page_context_path, $this->survey));
            }
        }
        else
        {
            //no pages added to survey !!
        //            $this->addPage(new SurveyQuestionViewerWizardPage('question_page_' . $page_nr, $this, $page_nr));
        }
    }

    function get_parent()
    {
        return $this->parent;
    }

    function get_survey()
    {
        return $this->survey;
    }
	
    function started(){
    	 $this->parent->started();
    }
    
    function finished(){
    	$this->parent->finished();
    }
    
    function save_answer($complex_question_id, $answer, $context_path)
    {
        $this->parent->save_answer($complex_question_id, $answer, $context_path);
    }

    function get_answer($complex_question_id, $context_path)
    {
        return $this->parent->get_answer($complex_question_id, $context_path);
    }
	
    function get_go_back_url(){
    	 return $this->parent->get_go_back_url();
    }
}
?>