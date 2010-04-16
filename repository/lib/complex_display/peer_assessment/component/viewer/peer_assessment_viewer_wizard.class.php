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
    private $total_questions;
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

        $this->total_pages = 0;

        while ($complex_content_object = $complex_content_objects->next_result())
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

    function get_total_questions()
    {
        return $this->total_questions;
    }

}
?>