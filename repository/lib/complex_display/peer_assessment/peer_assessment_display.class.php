<?php
/**
 * @author Nick Van Loocke
 */

require_once dirname(__FILE__) . '/peer_assessment_display_component.class.php';

class PeerAssessmentDisplay extends ComplexDisplay
{
    const ACTION_VIEW_PEER_ASSESSMENT = 'view_peer_assessment';
    const ACTION_VIEW_TOPIC = 'view_topic';
    const ACTION_PUBLISH_PEER_ASSESSMENT = 'publish';
    
    const ACTION_CREATE_PEER_ASSESSMENT_POST = 'add_post';
    const ACTION_EDIT_PEER_ASSESSMENT_POST = 'edit_post';
    const ACTION_DELETE_PEER_ASSESSMENT_POST = 'delete_post';
    const ACTION_QUOTE_PEER_ASSESSMENT_POST = 'quote_post';
    
    const ACTION_CREATE_TOPIC = 'create_topic';
    const ACTION_DELETE_TOPIC = 'delete_topic';
    
    const ACTION_CREATE_SUBPEER_ASSESSMENT = 'create_subpeer_assessment';
    const ACTION_EDIT_SUBPEER_ASSESSMENT = 'edit_subpeer_assessment';
    const ACTION_DELETE_SUBPEER_ASSESSMENT = 'delete_subpeer_assessment';
    const ACTION_MOVE_SUBPEER_ASSESSMENT = 'move_subpeer_assessment';
    
    const ACTION_MAKE_IMPORTANT = 'make_important';
    const ACTION_MAKE_STICKY = 'make_sticky';

    function run()
    {
        $action = $this->get_action();
        
        switch ($action)
        {
            case self :: ACTION_PUBLISH_PEER_ASSESSMENT :
                $component = PeerAssessmentDisplayComponent :: factory('Publisher', $this);
                break;
            case self :: ACTION_VIEW_PEER_ASSESSMENT :
                $component = PeerAssessmentDisplayComponent :: factory('PeerAssessmentViewer', $this);
                break;
            case self :: ACTION_VIEW_TOPIC :
                $component = PeerAssessmentDisplayComponent :: factory('TopicViewer', $this);
                break;
            case self :: ACTION_CREATE_PEER_ASSESSMENT_POST :
                $component = PeerAssessmentDisplayComponent :: factory('PeerAssessmentPostCreator', $this);
                break;
            case self :: ACTION_EDIT_PEER_ASSESSMENT_POST :
                $component = PeerAssessmentDisplayComponent :: factory('PeerAssessmentPostEditor', $this);
                break;
            case self :: ACTION_DELETE_PEER_ASSESSMENT_POST :
                $component = PeerAssessmentDisplayComponent :: factory('PeerAssessmentPostDeleter', $this);
                break;
            case self :: ACTION_QUOTE_PEER_ASSESSMENT_POST :
                $component = PeerAssessmentDisplayComponent :: factory('PeerAssessmentPostQuoter', $this);
                break;
            case self :: ACTION_CREATE_TOPIC :
                $component = PeerAssessmentDisplayComponent :: factory('PeerAssessmentTopicCreator', $this);
                break;
            case self :: ACTION_DELETE_TOPIC :
                $component = PeerAssessmentDisplayComponent :: factory('PeerAssessmentTopicDeleter', $this);
                break;
            case self :: ACTION_MOVE_SUBPEER_ASSESSMENT :
                $component = PeerAssessmentDisplayComponent :: factory('PeerAssessmentSubpeer_assessmentMover', $this);
                break;
            case self :: ACTION_CREATE_SUBPEER_ASSESSMENT :
                $component = PeerAssessmentDisplayComponent :: factory('PeerAssessmentSubpeer_assessmentCreator', $this);
                break;
            case self :: ACTION_EDIT_SUBPEER_ASSESSMENT :
                $component = PeerAssessmentDisplayComponent :: factory('PeerAssessmentSubpeer_assessmentEditor', $this);
                break;
            case self :: ACTION_DELETE_SUBPEER_ASSESSMENT :
                $component = PeerAssessmentDisplayComponent :: factory('PeerAssessmentSubpeer_assessmentDeleter', $this);
                break;
            case self :: ACTION_MAKE_IMPORTANT :
                $component = PeerAssessmentDisplayComponent :: factory('Important', $this);
                break;
            case self :: ACTION_MAKE_STICKY :
                $component = PeerAssessmentDisplayComponent :: factory('Sticky', $this);
                break;
            default :
                $this->set_action(self :: ACTION_VIEW_CLO);
                $component = PeerAssessmentDisplayComponent :: factory('PeerAssessmentViewer', $this);
        }
        
        if (! $component)
            parent :: run();
        else
            $component->run();
    }
}

?>