<?php
require_once dirname(__FILE__) . '/../peer_assessment_manager.class.php';

class PeerAssessmentManagerViewerComponent extends PeerAssessmentManager
{
    private $cd;
    private $trail;

    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        $this->trail = $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PEER_ASSESSMENT_PUBLICATIONS)), Translation :: get('PeerAssessment')));
        
        $this->set_parameter(PeerAssessmentManager :: PARAM_ACTION, PeerAssessmentManager :: ACTION_VIEW_PEER_ASSESSMENT);
        $this->set_parameter(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION, Request :: get(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION));
        
        ComplexDisplay :: launch(PeerAssessment :: get_type_name(), $this);
    }

    function get_current_attempt_id()
    {
        return $this->get_component_id();
    }

    function get_root_content_object()
    {
        $pub = PeerAssessmentDataManager :: get_instance()->retrieve_peer_assessment_publication(Request :: get(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION));
        return $pub->get_content_object();
    }

}
?>