<?php
require_once dirname(__FILE__) . '/../peer_assessment_manager.class.php';
require_once dirname(__FILE__) . '/../peer_assessment_manager_component.class.php';
require_once Path :: get_repository_path() . 'lib/complex_display/peer_assessment/peer_assessment_display.class.php';

class PeerAssessmentManagerPeerAssessmentViewerComponent extends PeerAssessmentManagerComponent
{
    private $cd;

    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PEER_ASSESSMENT_PUBLICATIONS)), Translation :: get('PeerAssessment')));
        
        $this->set_parameter(PeerAssessmentManager :: PARAM_ACTION, PeerAssessmentManager :: ACTION_VIEW_PEER_ASSESSMENT);
        $this->set_parameter(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION, Request :: get(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION));
        
        $this->cd = ComplexDisplay :: factory($this, 'peer_assessment');
        
        $pub = PeerAssessmentDataManager :: get_instance()->retrieve_peer_assessment_publication(Request :: get(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION));
        
        $this->cd->set_root_lo($pub->get_content_object());
        //$this->display_header($trail, false);
        $this->cd->run();
        //$this->display_footer();
    }

}
?>