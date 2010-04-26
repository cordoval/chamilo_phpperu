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
    //private $total_pages;
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
        
        //$this->total_pages = 0;
        $this->total_competences = 0;

        // To see the html on the next page
        //$this->total_pages = 1;
         
        while (($complex_content_object = $complex_content_objects->next_result()))
        {
            //$this->total_pages ++;
            $this->total_competences ++;
            $this->addPage(new QuestionsPeerAssessmentViewerWizardPage('question_page_' . $this->total_competences/*$this->total_pages*/, $this, $this->total_competences/*$this->total_pages*/));

            $peer_assessment_page = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_object->get_ref());
            $page_questions = $this->get_peer_assessment_page_questions($peer_assessment_page);
            $this->pages[$this->total_competences/*$this->total_pages*/] = array(page => $peer_assessment_page, questions => $page_questions);
        }
        
        if (/*$this->total_pages*/$this->total_competences == 0)
        {
            $this->addPage(new QuestionsPeerAssessmentViewerWizardPage('question_page_' . /*$this->total_pages*/$this->total_competences, $this, /*$this->total_pages*/$total_competences));
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
    
    
    // Competences
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
    
    
    // Indicators of a competence
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
       
    
    // Criterias of an indicator
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
    
    
    // Peer assessment publication
	function get_peer_assessment_publication($peer_assessment_id)
    {
        $peer_assessment_publication = PeerAssessmentDataManager :: get_instance()->retrieve_peer_assessment_publication($peer_assessment_id);
        return $peer_assessment_publication;
    }
    
    
    // Groups
	function get_peer_assessment_publication_groups($peer_assessment_id)
    {
        $condition = new EqualityCondition(PeerAssessmentPublicationGroup :: PROPERTY_PEER_ASSESSMENT_PUBLICATION, $peer_assessment_id);     
        $peer_assessment_publication_groups = PeerAssessmentDataManager :: get_instance()->retrieve_peer_assessment_publication_groups($condition);
        return $peer_assessment_publication_groups;
    }
    
	function get_group($group_id)
    {
        $group = GroupDataManager :: get_instance()->retrieve_group($group_id);
        return $group;
    }
    
    
    // Users
	function get_peer_assessment_publication_users($peer_assessment_id)
    {
    	$condition = new EqualityCondition(PeerAssessmentPublicationUser :: PROPERTY_PEER_ASSESSMENT_PUBLICATION, $peer_assessment_id);     
        $peer_assessment_publication_users = PeerAssessmentDataManager :: get_instance()->retrieve_peer_assessment_publication_users($condition);
        return $peer_assessment_publication_users;
    }
    
	function get_user($user_id)
    {
        $user = UserDataManager :: get_instance()->retrieve_user($user_id);
        return $user;
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