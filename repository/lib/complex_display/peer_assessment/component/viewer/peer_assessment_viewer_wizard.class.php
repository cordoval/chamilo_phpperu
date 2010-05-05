<?php
/*
 *	@author Nick Van Loocke
 */
require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';

require_once dirname(__FILE__) . '/wizard/peer_assessment_viewer_wizard_display.class.php';
require_once dirname(__FILE__) . '/wizard/peer_assessment_viewer_wizard_process.class.php';
require_once dirname(__FILE__) . '/wizard/peer_assessment_viewer_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/competences_peer_assessment_viewer_wizard_page.class.php';

class PeerAssessmentViewerWizard extends HTML_QuickForm_Controller
{
    private $parent;
    private $peer_assessment;
    private $total;

    function PeerAssessmentViewerWizard($parent, $peer_assessment)
    {
    	$id = $_GET[PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION];
        //parent :: HTML_QuickForm_Controller('PeerAssessmentViewerWizard_' . $parent->get_current_attempt_id(), true);

        $this->parent = $parent;
        $this->peer_assessment = $peer_assessment;
        $this->add_pages();

        $this->addAction('display', new PeerAssessmentViewerWizardDisplay($this));

    }

    function add_pages()
    {   	
    	$this->total = 0;
        $complex_content_objects = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->peer_assessment->get_id(), ComplexContentObjectItem :: get_table_name()));
        
        while (($complex_content_object = $complex_content_objects->next_result()))
        {
            $this->total ++;

            $competence_id = Request :: get('competence');
            if($competence_id == null)
            {
            	$this->addPage(new CompetencesPeerAssessmentViewerWizardPage('competences_page_' . $this->total, $this, $this->total));
            	$peer_assessment_page = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_object->get_ref());           
            }
            else
            {
            	$this->addPage(new IndicatorsPeerAssessmentViewerWizardPage('indicators_page_' . $this->total, $this, $this->total));
            	$peer_assessment_page = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_object->get_ref());           
            }

        }
    }  

    
    // Peer assessment publication
	function get_peer_assessment_publication($peer_assessment_id)
    {
        $peer_assessment_publication = PeerAssessmentDataManager :: get_instance()->retrieve_peer_assessment_publication($peer_assessment_id);
        return $peer_assessment_publication;
    }
    
    
    // Competences
	function get_peer_assessment_page_competences($peer_assessment_page)
    {
        $complex_content_objects = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $peer_assessment_page->get_id(), ComplexContentObjectItem :: get_table_name()));
        $competences = array();

        while ($complex_content_object = $complex_content_objects->next_result())
        {
            $this->total ++;
            $competence = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_object->get_ref());
            $competences[$this->total] = $competence;
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
    
    
    // Retrieves one result row
    function get_peer_assessment_publication_result($publication_id, $competence_id, $indicator_id, $user_id, $graded_user_id)
    {
    	$peer_assessment_publication_result = PeerAssessmentDataManager :: get_instance()->retrieve_peer_assessment_publication_result($publication_id, $competence_id, $indicator_id, $user_id, $graded_user_id);
        return $peer_assessment_publication_result;
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

    
	// Parent
    function get_parent()
    {
        return $this->parent;
    }

    // Peer assessment
    function get_peer_assessment()
    {
        return $this->peer_assessment;
    }
   
    // Total pages
    function get_total()
    {
    	return $this->total;
    }
}
?>