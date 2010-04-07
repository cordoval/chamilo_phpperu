<?php
require_once dirname(__FILE__) . '/../peer_assessment_manager.class.php';
require_once dirname(__FILE__) . '/../peer_assessment_manager_component.class.php';
/**
 * @author Nick Van Loocke
 */

class PeerAssessmentManagerViewerComponent extends PeerAssessmentManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_parent()->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE)), Translation :: get('BrowsePeerAssessment')));
        
        $pid = Request :: get('pid');
        $trail->add(new Breadcrumb($this->get_url(array('display_action' => 'view_peer_assessment', 'pid' => $pid)), Translation :: get('ViewPeerAssessment')));
        
        $cid = Request :: get('cid');
        
        $this->display_header($trail);
        
        $cd = ComplexDisplay :: factory($this, 'peer_assessment');
        $cd->run();
        
        $this->display_footer();
        
        switch ($cd->get_action())
        {
            case PeerAssessmentDisplay :: ACTION_VIEW_TOPIC :
                Events :: trigger_event('view_peer_assessment_topic', 'weblcms', array('user_id' => $this->get_user_id(), 'publication_id' => $pid, 'peer_assessment_topic_id' => $cid));
                break;
        }
    }

    function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        $parameters[PeerAssessmentManager :: PARAM_ACTION] = PeerAssessmentManager :: ACTION_VIEW;
        return $this->get_parent()->get_url($parameters, $filter, $encode_entities);
    }

    function redirect($message = null, $error_message = false, $parameters = array(), $filter = array(), $encode_entities = false)
    {
        $parameters[PeerAssessmentManager :: PARAM_ACTION] = PeerAssessmentManager :: ACTION_VIEW;
        $this->get_parent()->redirect($message, $error_message, $parameters, $filter, $encode_entities);
    }
}
?>