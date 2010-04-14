<?php
require_once dirname(__FILE__) . '/../peer_assessment_display.class.php';
require_once dirname(__FILE__) . '/../peer_assessment_display_component.class.php';
require_once dirname(__FILE__) . '/viewer/peer_assessment_viewer_wizard.class.php';

class PeerAssessmentDisplayPeerAssessmentViewerComponent extends PeerAssessmentDisplayComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $wizard = new PeerAssessmentViewerWizard($this, $this->get_root_lo());
        return $wizard->run();
    }
}
?>