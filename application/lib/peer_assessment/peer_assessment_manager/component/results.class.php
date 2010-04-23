<?php
require_once dirname(__FILE__) . '/../peer_assessment_manager.class.php';
/**
 * Component to view the results of the peer assessments
 * @author Nick Van Loocke
 */

class PeerAssessmentManagerResultsComponent extends PeerAssessmentManager
{
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
        
		$form = $this->build_result_form($pids);	
        if ($form->validate())
        {
            $this->redirect(Translation :: get('PeerAssessmentChecked'), false, array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS));
        }
        else
        {
            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb($this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowsePeerAssessmentPublications')));
            $trail->add(new Breadcrumb($this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_MOVE_PEER_ASSESSMENT_PUBLICATION, PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $pid)), Translation :: get('PeerAssessmentPublicationResults')));
            
            $this->display_header($trail, true);
            
            echo $form->toHtml();
            $this->display_footer();
        }
    }

    function build_result_form($pids)
    {
        $url = $this->get_url(array(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $pids));
        $form = new FormValidator('peer_assessment_publication_mover', 'post', $url);
        
        return $form;
	} 
}

?>