<?php
require_once dirname(__FILE__) . '/../peer_assessment_manager.class.php';
/**
 * Component to view the results of the peer assessments
 * @author Nick Van Loocke
 */

class PeerAssessmentManagerResultsComponent extends PeerAssessmentManager
{
	private $datamanager;
	private $peer_assessment;
	private $pid;
	
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
     
		$form = $this->build_result_form($pids);	
        if ($form->validate())
        {
            $this->redirect(Translation :: get('PeerAssessmentChecked'), false, array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS));
        }
        else
        {
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(new Breadcrumb($this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowsePeerAssessmentPublications')));
            $trail->add(new Breadcrumb($this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_VIEW_PEER_ASSESSMENT_PUBLICATION_RESULTS, PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $pid)), Translation :: get('PeerAssessmentPublicationResults')));
            
            $this->display_header($trail, true);

            $display = ComplexDisplay :: factory($this, PeerAssessment :: get_type_name());
            $display->set_action(PeerAssessmentDisplay :: ACTION_VIEW_PEER_ASSESSMENT_RESULTS);
        	$display->run();
            
            echo $form->toHtml();
            
            $this->display_footer();
        }
    }

    function build_result_form($pids)
    {
        $url = $this->get_url(array(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $pids));
        $form = new FormValidator('view_peer_assessment_publication_results', 'post', $url);
        
        return $form;
	} 
	
	function get_root_content_object()
	{
		return $this->peer_assessment;
	}
}

?>