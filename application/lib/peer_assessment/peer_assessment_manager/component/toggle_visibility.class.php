<?php
/**
 *	@author Nick Van Loocke
 */

class PeerAssessmentManagerToggleVisibilityComponent extends PeerAssessmentManagerComponent
{

    function run()
    {
        if ($this->is_allowed(DELETE_RIGHT))
        {
            if (Request :: get(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION))
            {
                $publication_ids = Request :: get(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION);
            }
            else
            {
                $publication_ids = $_POST[PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION];
            }
            
            if (! is_array($publication_ids))
            {
                $publication_ids = array($publication_ids);
            }
            
            $datamanager = PeerAssessmentDataManager :: get_instance();
            
            foreach ($publication_ids as $index => $pid)
            {
                $publication = $datamanager->retrieve_peer_assessment_publication($pid);
                
                if (Request :: get(PARAM_VISIBILITY))
                {
                    $publication->set_hidden(Request :: get(PARAM_VISIBILITY));
                }
                else
                {
                    $publication->toggle_visibility();
                }
                
                $publication->update();
            }
            
            if (count($publication_ids) > 1)
            {
                $message = htmlentities(Translation :: get('ContentObjectPublicationsVisibilityChanged'));
            }
            else
            {
                $message = htmlentities(Translation :: get('ContentObjectPublicationVisibilityChanged'));
            }
            
            $params = array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE);

            $this->redirect($message, '', $params);
            
            $this->redirect($message, false, $params);
        }
    }
}
?>
