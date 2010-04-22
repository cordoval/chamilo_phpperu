<?php
require_once dirname(__FILE__) . '/../peer_assessment_manager.class.php';
/**
 * Component to complete a peer assessment
 * @author Nick Van Loocke
 */

class PeerAssessmentManagerTakeComponent extends PeerAssessmentManager
{	
	private $datamanager;
	private $peer_assessment;
	private $pid;
	private $pub;
	
	function run()
	{	
		$pid = Request :: get('peer_assessment_publication');
        if (! $pid || (is_array($pid) && count($pid) == 0))
        {
            $this->not_allowed();
            exit();
        }
        $pids = $pid;
        
        if (is_array($pids))
        {
            $pid = $pids[0];
        }
        
        
        $publication = $this->retrieve_peer_assessment_publication($pid);     
		if (! $publication->is_visible_for_target_user($this->get_user()))
        {
            $this->not_allowed($trail, false);
        }       

        $this->datamanager = PeerAssessmentDataManager :: get_instance();
		
		$this->pid = Request :: get(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION);
        $this->pub = $this->datamanager->retrieve_peer_assessment_publication($this->pid);
        $peer_assessment_id = $publication->get_content_object()->get_object_number();
        $this->peer_assessment = RepositoryDataManager :: get_instance()->retrieve_content_object($peer_assessment_id);
        $this->set_parameter(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION, $this->pid);
        
               
		$form = $this->build_result_form(/*$publication,*/ $pids);	
        if ($form->validate())
        {
            $this->redirect(Translation :: get('PeerAssessmentChecked'), false, array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS));
        }
        else
        {
            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb($this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowsePeerAssessmentPublications')));
            $trail->add(new Breadcrumb($this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_MOVE_PEER_ASSESSMENT_PUBLICATION, PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $pid)), Translation :: get('TakePeerAssessment')));
            
            $this->display_header($trail, true);
            
            $display = ComplexDisplay :: factory($this, PeerAssessment :: get_type_name());
        	$display->set_root_lo($this->peer_assessment);
        	$display->run();
            
            echo $form->toHtml();
            //$this->display_footer();
        }

    }
    
    function build_result_form(/*$publication,*/ $pids)
    {
        $url = $this->get_url(array(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $pids));
        $form = new FormValidator('take_peer_assessment_publication', 'post', $url);
        
        
        //$form->addElement('static', Criteria :: PROPERTY_TITLE, $publication->get_content_object()->get_title());
        //$form->addElement('static', Criteria :: PROPERTY_TITLE, $publication->get_content_object()->get_description());

        return $form;
	}  

    function get_current_attempt_id()
    {
        return $this->peer_assessment->get_id();
    }
}
?>