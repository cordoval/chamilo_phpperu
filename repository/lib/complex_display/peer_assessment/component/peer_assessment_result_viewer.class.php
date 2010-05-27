<?php
/**
 * @author Nick Van Loocke
 */
require_once dirname(__FILE__) . '/../peer_assessment_display.class.php';
require_once dirname(__FILE__) . '/result_viewer/peer_assessment_result_viewer.class.php';

class PeerAssessmentDisplayPeerAssessmentResultViewerComponent extends PeerAssessmentDisplay
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $wizard = new PeerAssessmentResultViewerWizard($this, $this->get_root_lo());
        return $wizard->run();
    }
}
?>