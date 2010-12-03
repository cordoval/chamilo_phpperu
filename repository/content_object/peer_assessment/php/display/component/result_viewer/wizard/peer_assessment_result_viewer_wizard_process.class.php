<?php
namespace repository\content_object\peer_assessment;
/*
 *	@author Nick Van Loocke
 */
use \HTML_QuickForm_Action;

class PeerAssessmentResultViewerWizardProcess extends HTML_QuickForm_Action
{
    private $parent;

    public function __construct($parent)
    {
        $this->parent = $parent->get_parent();
		$this->perform();
    }

    function perform()
    {  
    	
    }

}
?>