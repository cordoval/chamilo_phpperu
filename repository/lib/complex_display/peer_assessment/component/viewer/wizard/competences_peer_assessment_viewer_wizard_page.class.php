<?php
require_once dirname(__FILE__) . '/../../../../peer_assessment/component/viewer/wizard/inc/peer_assessment_question_display.class.php';

class CompetencesPeerAssessmentViewerWizardPage extends PeerAssessmentViewerWizardPage
{
    private $page_number;

    function CompetencesPeerAssessmentViewerWizardPage($name, $parent, $page_number)
    {
        parent :: PeerAssessmentViewerWizardPage($name, $parent);
        $this->page_number = $page_number;
        $this->addAction('process', new PeerAssessmentViewerWizardProcess($this));
    }

    function buildForm()
    {
    	//dump($_POST);
        $this->_formBuilt = true;    
        
        // *********************************		
        // Prints of the list of competences
        // ********************************* 

    	$html[] = '<div class="assessment">';
	            
        // Peer assessment title en description
        $html[] = '<h2>' . $this->get_parent()->get_peer_assessment()->get_title() . '</h2>';
            
        if ($this->get_parent()->get_peer_assessment()->has_description())
        {
            $html[] = '<div class="description">';
            $html[] = $this->get_parent()->get_peer_assessment()->get_description();
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
        }
        
        $publication_id = Request :: get('peer_assessment_publication');
        $type = Request :: get('go');

        // Content object id
	    $peer_assessment_id = $this->get_parent()->get_peer_assessment()->get_id();
	
        // Publication object     	
	    $peer_assessment_publication = $this->get_parent()->get_peer_assessment_publication($publication_id);
        
	    // Groups
        $groups = $this->get_parent()->get_peer_assessment_publication_groups($publication_id)->as_array();
        $count_groups = sizeof($this->get_parent()->get_peer_assessment_publication_groups($publication_id)->as_array());
			          	    
	    // Users
		$users = $this->get_parent()->get_peer_assessment_publication_users($publication_id)->as_array();   
		$count_users = sizeof($this->get_parent()->get_peer_assessment_publication_users($publication_id)->as_array());
	    
        
		
		if($count_users == 0)
        {
            $html[] = '<div class="clear"></div>';
        	$html[] = '<div class="error-message">';
        	$html[] = Translation :: get('NoUsersInThePeerAssessment');
        	$html[] = '<div class="close_message" id="closeMessage"></div>';
        	$html[] = '</div>';
        }
		else
		{
			// Retrieve competences
	        $competences = $this->get_parent()->get_peer_assessment_page_competences($this->get_parent()->get_peer_assessment());
	        
	        $count = 0;
	        
            foreach($competences as $competence)
            {
            	$count++;
            	
            	// Retrieve indicators
            	$indicators = $this->get_parent()->get_peer_assessment_page_indicators_via_competence($this->get_parent()->get_peer_assessment(), $competence);
            	// From date - To date
            	$from_date = $peer_assessment_publication->get_from_date();
            	$to_date = $peer_assessment_publication->get_to_date();
            	
            	if(($from_date == 0) && ($to_date == 0))
            	{
            		$date_message = Translation :: get('AlwaysOpen');
            	}
            	else
            	{
            		$from_date = DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), $from_date);
            		$to_date = DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), $to_date);
            		$date_message = Translation :: get('From') . ' ' .$from_date . ' - ' . Translation :: get('To') . ' ' . $to_date;
            	}
            	
            	$html[] = '<br/>';
				$html[] = '<div class="question">';
		        $html[] = '<div class="title">';
		        $html[] = '<div class="number">';
		        $html[] = '<div class="bevel">';
		        $html[] = $count. '.';
		        $html[] = '</div>';
		        $html[] = '</div>';
		        $html[] = '<div class="text">';
		        
		        $html[] = '<div class="bevel" style="float: left;">';
		        $html[] = '<img src="'. Theme :: get_common_image_path() . 'content_object/competence.png' .'" alt=""/>';
		        $html[] = $competence->get_title();
		        $html[] = '</div>';
		        $html[] = '<div class="bevel" style="text-align: right;">';
		        $html[] = $date_message;
		        $html[] = '<div class="clear"></div>';
		        $html[] = '</div>';
		        
		        $html[] = '</div>';
		        $html[] = '<div class="clear"></div>';
		        $html[] = '</div>';
		        $html[] = '<div class="answer">';
		        
	            $html[] = '<div class="description">';
	            $html[] = $competence->get_description();
	            $html[] = '<div class="clear"></div>';
	            $html[] = '</div>';
	            $html[] = $this->take_peer_assessment($users, $indicators);
	            $html[] = '</div>';
		        $html[] = '</div>';
		        
		        $html[] = '<div class="clear"></div>';
		        
            }
		}	

		
		
        // Prints of the $html array    	
        echo implode("\n", $html);
    }
    
    
    
    function take_peer_assessment($users, $indicators)
    {
    	$html[] = '<table class="data_table take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th></th>';
        
        foreach ($users as $user)
        {
        	$user_id = $user->get_user();     	
           	$selected_user = $this->get_parent()->get_user($user_id);
           	$full_user_name = $selected_user->get_firstname() .' '. $selected_user->get_lastname();
            $html[] = '<th>' . $full_user_name . '</th>';
        }
        
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        
        
        
        foreach ($indicators as $indicator)
        {
        	$html[] = '<tr>';
        	$html[] = '<td>';
        	$html[] = $indicator->get_title();
        	$html[] = '</td>';
	        foreach ($users as $user)
	        {
	        	$html[] = '<td>';
	        	$html[] = '</td>';
	        }
	        $html[] = '</tr>';
        }
        
        
        $html[] = '</tbody>';
        $html[] = '</table>';

        return implode("\n", $html);
    }
    
}
?>