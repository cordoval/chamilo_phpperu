<?php
/*
 *	@author Nick Van Loocke
 */

class PeerAssessmentViewerWizardProcess extends HTML_QuickForm_Action
{
    private $parent;

    public function PeerAssessmentViewerWizardProcess($parent)
    {
        $this->parent = $parent->get_parent();
		$this->perform();
    }

    function perform()
    {       		
    	if(sizeof($_POST[select]) > 0)
    	{	
        	// Publication id
    		$publication_id = Request :: get('peer_assessment_publication');
    		// Users
    		$users = $this->parent->get_peer_assessment_publication_users($publication_id)->as_array();
    		 
    		
    		// Retrieve competences
	        $competences = $this->parent->get_peer_assessment_page_competences($this->parent->get_peer_assessment());

            foreach($competences as $competence)
            {
				// Retrieve indicators of the selected competence
				$indicators = $this->parent->get_peer_assessment_page_indicators_via_competence($this->parent->get_peer_assessment(), $competence);			
	
	    		foreach($indicators as $indicator)
	    		{
		    		foreach($users as $user)
		    		{
		    			$competence_id = $competence->get_id();
		    			$indicator_id = $indicator->get_id();
		    			$user_id = Session :: get_user_id();
		    			$graded_user_id = $user->get_user();
		    			
		    			$value = ($_POST[select][c.$competence_id.i.$indicator_id.u.$graded_user_id]);

		    			$publication_result = $this->parent->get_peer_assessment_publication_result($publication_id, $competence_id, $indicator_id, $user_id, $graded_user_id);
						
		    			if($publication_result == null)
		    			{
		    				// Create
			    			$results = new PeerAssessmentPublicationResults();
				    		$results->set_publication_id($publication_id);
				    		$results->set_competence_id($competence_id);
				    		$results->set_indicator_id($indicator_id);
				    		$results->set_user_id($user_id);
				    		$results->set_graded_user_id($graded_user_id);
				    		$results->set_score($value);
				    		if($value != 0)
				    		{
				    			$results->set_finished(1);
				    		}
				    		
				    		$results->create();
		    			}
		    			else
						{
							// Update
							if($value != $publication_result->get_score())
							{
					    		$publication_result->set_score($value);
					    		if($value != 0)
					    		{
					    			$publication_result->set_finished(1);
					    		}
					    		$publication_result->update();
							}
						}

		    		}  
	    		}
            }
            
    		if($results)
    		{
		        $message = Translation :: get('PeerAssessmentResultsCreated');
    		}
    		elseif($publication_result)
    		{
    			$message = Translation :: get('PeerAssessmentResultsUpdated');	
    		}
    		
    		$error[] = '<div class="clear"></div>';
        	$error[] = '<div class="normal-message">';
        	$error[] = $message;
        	$error[] = '<div class="close_message" id="closeMessage"></div>';
        	$error[] = '</div>';
        	
        	echo implode("\n", $error);
    		
    		//$this->parent->get_parent()->redirect($message, false, array('submitted'));  
    	}
    }

}
?>