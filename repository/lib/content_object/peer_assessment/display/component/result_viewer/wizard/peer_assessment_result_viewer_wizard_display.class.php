<?php
require_once dirname(__FILE__) . '/../../../../../../../application/lib/peer_assessment/peer_assessment_publication_results.class.php';

/**
 * @author Nick Van Loocke
 */

class PeerAssessmentResultViewerWizardDisplay extends HTML_QuickForm_Action_Display
{
    private $parent;       								    	

    public function PeerAssessmentResultViewerWizardDisplay($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Displays the HTML-code of a page in the wizard
     * @param HTML_Quickform_Page $page The page to display.
     */
    function _renderForm($current_page)
    {   	    		
		parent :: _renderForm($current_page);
    }
}
?>