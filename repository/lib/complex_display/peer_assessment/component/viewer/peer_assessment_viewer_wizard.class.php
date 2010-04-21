<?php

require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';

require_once dirname(__FILE__) . '/wizard/peer_assessment_viewer_wizard_display.class.php';
require_once dirname(__FILE__) . '/wizard/peer_assessment_viewer_wizard_process.class.php';
require_once dirname(__FILE__) . '/wizard/peer_assessment_viewer_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/questions_peer_assessment_viewer_wizard_page.class.php';

class PeerAssessmentViewerWizard extends HTML_QuickForm_Controller
{

    private $parent;
    private $peer_assessment;
    private $total_pages;
    //private $total_questions;
    private $total_competences;
    private $total_indicators;
    private $total_criterias;
    private $pages;

    function PeerAssessmentViewerWizard($parent, $peer_assessment)
    {
    	$id = $_GET[PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION];
        parent :: HTML_QuickForm_Controller('PeerAssessmentViewerWizard_' . $parent->get_current_attempt_id(), true);

        $this->parent = $parent;
        $this->peer_assessment = $peer_assessment;

        $this->add_pages();

        $this->addAction('process', new PeerAssessmentViewerWizardProcess($this));
        $this->addAction('display', new PeerAssessmentViewerWizardDisplay($this));

    }

    function add_pages()
    {

        $complex_content_objects = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->peer_assessment->get_id(), ComplexContentObjectItem :: get_table_name()));
        
        /*$count = $this->total_pages = 2;
        
        for($i = 0; $i > $count; $i++)
        {
        	while ($complex_content_object = $complex_content_objects->next_result())
        	{
				$this->addPage(new QuestionsPeerAssessmentViewerWizardPage('question_page_' . $this->total_pages, $this, $this->total_pages));
        		  		
        		$peer_assessment_page = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_object->get_ref());
        		$page_questions = $this->get_peer_assessment_page_questions($peer_assessment_page);
        		$this->pages[$i + 1] = array(page => $peer_assessment_page, questions => $page_questions);
        	}
        }*/
        
        $this->total_pages = 0;
         
        while (($complex_content_object = $complex_content_objects->next_result()) && ($this->total_pages < 2))
        {
            $this->total_pages ++;
            $this->addPage(new QuestionsPeerAssessmentViewerWizardPage('question_page_' . $this->total_pages, $this, $this->total_pages));

            $peer_assessment_page = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_object->get_ref());
            $page_questions = $this->get_peer_assessment_page_questions($peer_assessment_page);
            $this->pages[$this->total_pages] = array(page => $peer_assessment_page, questions => $page_questions);
        }
        
        if ($this->total_pages == 0)
        {
            $this->addPage(new QuestionsPeerAssessmentViewerWizardPage('question_page_' . $this->total_pages, $this, $this->total_pages));
        }

    }

    function get_peer_assessment_page_questions($peer_assessment_page)
    {

        $complex_content_objects = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $peer_assessment_page->get_id(), ComplexContentObjectItem :: get_table_name()));
        $questions = array();

        while ($complex_content_object = $complex_content_objects->next_result())
        {
            $this->total_questions ++;
            $question = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_object->get_ref());
            $questions[$this->total_questions] = $question;

        }

        return $questions;

    }
    
    // Return the competences of an peer assessment
	function get_peer_assessment_page_competences($peer_assessment_page)
    {
        $complex_content_objects = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $peer_assessment_page->get_id(), ComplexContentObjectItem :: get_table_name()));
        $competences = array();

        while ($complex_content_object = $complex_content_objects->next_result())
        {
            $this->total_competences ++;
            $competence = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_object->get_ref());
            $competences[$this->total_competences] = $competence;
        }
        return $competences;
    }
    
    // Returns the indicators of an competence
    function get_peer_assessment_page_indicators_via_competence($peer_assessment_page, $competence)
    {
    	$complex_content_objects = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $competence->get_id(), ComplexContentObjectItem :: get_table_name()));
    	$indicators = array();

        while ($complex_content_object = $complex_content_objects->next_result())
        {
            $this->total_indicators ++;
            $indicator = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_object->get_ref());
            $indicators[$this->total_indicators] = $indicator;         
        }
        return $indicators;
    }
       
    // Returns the criteria of an indicator
	function get_peer_assessment_page_criterias_via_indicator($peer_assessment_page, $indicator)
    {
    	$complex_content_objects = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $indicator->get_id(), ComplexContentObjectItem :: get_table_name()));
    	$criterias = array();

        while ($complex_content_object = $complex_content_objects->next_result())
        {
            $this->total_criterias ++;
            $criteria = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_object->get_ref());
            $criterias[$this->total_criterias] = $criteria;         
        }
        return $criterias;
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

    function get_peer_assessment()
    {
        return $this->peer_assessment;
    }

    function get_total_pages()
    {
        return $this->total_pages;
    }

    /*function get_total_questions()
    {
        return $this->total_questions;
    }*/
    
    function get_total_competences()
    {
    	return $this->total_competences;
    }
    
    function get_total_indicators()
    {
    	return $this->total_indicators;
    }

    function get_total_criterias()
    {
    	return $this->total_criterias;
    }
}
?>