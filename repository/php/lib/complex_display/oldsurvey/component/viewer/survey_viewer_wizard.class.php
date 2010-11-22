<?php
namespace repository;

use common\libraries\EqualityCondition;
use common\libraries\AndCondition;

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
    private $question_visibility;

    function __construct($parent, $survey, $template_id)
    {
        parent :: __construct('SurveyViewerWizard_' . $survey->get_id(), true);

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

    	$complex_survey_page_items = $this->survey->get_pages(true);
        $page_nr = 0;
        $question_nr = 0;
        $this->question_visibility = array();

        while ($survey_page_item = $complex_survey_page_items->next_result())
        {
            if(! in_array($survey_page_item->get_ref(), $allowed_pages)){
            	continue;
            }

            $survey_page = RepositoryDataManager::get_instance()->retrieve_content_object($survey_page_item->get_ref());

        	$page_nr ++;
            $this->real_pages[$page_nr] = $survey_page->get_id();
        	$this->addPage(new QuestionsSurveyViewerWizardPage('question_page_' . $page_nr, $this, $page_nr));
            $questions = array();
            $questions_items = $survey_page->get_questions(true);

            while ($question_item =  $questions_items->next_result())
            {
				$question = RepositoryDataManager::get_instance()->retrieve_content_object($question_item->get_ref());

            	if($question_item->get_visible() == 1){
            		$this->question_visibility[$question->get_id()] = true;
            	}else{
            		$this->question_visibility[$question->get_id()] = false;
            	}



            	if ($question->get_type() == SurveyDescription :: get_type_name())
                {
                    $questions[$question->get_id() . 'description'] = $question;
                }
                else
                {
                    if($question_item->get_visible() == 1){
                    	$question_nr ++;
                    $questions[$question_nr] = $question;
                    }else{
//                    	$question_nr ++;
					$bis_nr = $question_nr.'.1';
                    $questions[$bis_nr] = $question;
                    }

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

    function get_real_page_nr($page_nr){
    	return $this->real_pages[$page_nr];
    }

    function get_question_visibility($question_id){
    	return $this->question_visibility[$question_id];
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
        $count = 0;

        foreach ($this->question_visibility as $visible) {
        	if($visible){
        		$count = $count + 1;
        	}
        }

        return $count;
    }

}
?>