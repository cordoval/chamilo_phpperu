<?php
namespace repository\content_object\peer_assessment;
/*
 *	@author Nick Van Loocke
 */

class PeerAssessmentResultViewerWizardProcess extends HTML_QuickForm_Action
{
    private $parent;

    public function PeerAssessmentResultViewerWizardProcess($parent)
    {
        $this->parent = $parent->get_parent();
		$this->perform();
    }

    function perform()
    {  
    	
    }

}
?>