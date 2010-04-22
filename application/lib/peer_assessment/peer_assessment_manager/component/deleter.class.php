<?php
require_once dirname(__FILE__) . '/../peer_assessment_manager.class.php';

/**
 * Component to delete peer_assessment_publications objects
 * @author Nick Van Loocke
 */
class PeerAssessmentManagerDeleterComponent extends PeerAssessmentManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = $_GET[PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION];
        $peer_assessment_publication = $this->retrieve_peer_assessment_publication($ids);
        $category_id = $peer_assessment_publication->get_category();

        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $peer_assessment_publication = $this->retrieve_peer_assessment_publication($id);
                
                if (! $peer_assessment_publication->delete())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedPeerAssessmentPublicationDeleted';
                }
                else
                {
                    $message = 'SelectedPeerAssessmentPublicationDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedPeerAssessmentPublicationsDeleted';
                }
                else
                {
                    $message = 'SelectedPeerAssessmentPublicationsDeleted';
                }
            }          
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS, 'category' => $category_id));
		}
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoPeerAssessmentPublicationsSelected')));
        }
    }
}
?>