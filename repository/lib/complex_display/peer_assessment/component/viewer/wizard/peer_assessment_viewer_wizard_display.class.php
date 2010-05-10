<?php
require_once dirname(__FILE__) . '/../../../../../../../application/lib/peer_assessment/peer_assessment_publication_results.class.php';

/**
 * @author Sven Vanpoucke
 * @author Nick Van Loocke
 */

class PeerAssessmentViewerWizardDisplay extends HTML_QuickForm_Action_Display
{
    private $parent;       								    	

    public function PeerAssessmentViewerWizardDisplay($parent)
    {
    	// Publication id
    	$publication_id = Request :: get('peer_assessment_publication');
    	// Users
		$users = $parent->get_peer_assessment_publication_users($publication_id)->as_array();   
		$count_users = sizeof($parent->get_peer_assessment_publication_users($publication_id)->as_array());

		// No users => gives back error message
		if($users == null)
		{
			$error[] = '<div class="clear"></div>';
        	$error[] = '<div class="error-message">';
        	$error[] = Translation :: get('NoUsersInThePeerAssessment');
        	$error[] = '<div class="close_message" id="closeMessage"></div>';
        	$error[] = '</div>';
        	
        	echo implode("\n", $error);
		}

		// No competences => gives back error message
    	if($parent->get_total() == 0)
    	{
    		$error[] = '<div class="clear"></div>';
        	$error[] = '<div class="error-message">';
        	$error[] = Translation :: get('NoCompetencesInThePeerAssessment');
        	$error[] = '<div class="close_message" id="closeMessage"></div>';
        	$error[] = '</div>';
        	
        	echo implode("\n", $error);
        	exit();
    	}	
    	
        $this->parent = $parent;
    }

    /**
     * Displays the HTML-code of a page in the wizard
     * @param HTML_Quickform_Page $page The page to display.
     */
    function _renderForm($current_page)
    {   	
    	// Publication id
    	$publication_id = Request :: get('peer_assessment_publication');
    	// Number of users
		$count_users = sizeof($current_page->get_parent()->get_peer_assessment_publication_users($publication_id)); 
		
		if($count_users > 1)
		{
			parent :: _renderForm($current_page);
		}		
    }
}
?>