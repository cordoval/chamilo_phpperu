<?php
/**
 *	@author Nick Van Loocke
 */

class PeerAssessmentManagerMoverComponent extends PeerAssessmentManagerComponent
{

    function run()
    {
        if ($this->get_parent()->is_allowed(EDIT_RIGHT))
        {
            $move = 0;
            $fpid = Request :: get(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION);
            if (Request :: get(PeerAssessmentManager :: PARAM_MOVE))
            {
                $move = Request :: get(PeerAssessmentManager :: PARAM_MOVE);
            }
            
            $datamanager = PeerAssessmentDataManager :: get_instance();
            $publication = $datamanager->retrieve_peer_assessment_publication($fpid);
            if ($publication->move($move))
            {
                $message = htmlentities(Translation :: get('ContentObjectPublicationMoved'));
            }
            $this->redirect($message, false, array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE));
        }
    }
}
?>
