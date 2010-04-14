<?php
require_once dirname(__FILE__) . '/../peer_assessment_manager.class.php';
require_once dirname(__FILE__) . '/../peer_assessment_manager_component.class.php';

/**
 * Component to create a new peer_assessment_publication object
 * @author Sven Vanpoucke
 * @author Nick Van Loocke
 */
class PeerAssessmentManagerPeerAssessmentPublicationVisibilityChangerComponent extends PeerAssessmentManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $pid = Request :: get('peer_assessment_publication');
               
        if ($pid)
        {
            $publication = $this->retrieve_peer_assessment_publication($pid);
            
            if (! $publication->is_visible_for_target_user($this->get_user()))
            {
                $this->redirect(null, false, array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS));
            }
            $publication->toggle_visibility();
            
            $publication->set_content_object($publication->get_content_object()->get_id());           
            $succes = $publication->update();
            
            $message = $succes ? 'VisibilityChanged' : 'VisibilityNotChanged';     

            $category_id = $publication->get_category();
            $this->redirect(Translation :: get($message), ! $succes, array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS, 'category' => $category_id));
        }
        else
        {
            $this->redirect(Translation :: get('NoPublicationSelected'), true, array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS));
        }
    }
}
?>