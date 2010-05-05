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
            $html[] = '<div style="float: right; margin-top: -15px;">'.$date_message.'</div>';
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
        }
		
		
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
            	if($count > 0)
            	{
            		unset($html);
            	}
            	$count++;
            	
            	// Retrieve indicators
            	$indicators = $this->get_parent()->get_peer_assessment_page_indicators_via_competence($this->get_parent()->get_peer_assessment(), $competence);
            	
            	
            	$html[] = '<br/>';
				$html[] = '<div class="question">';
		        $html[] = '<div class="title">';
		        $html[] = '<div class="number">';
		        $html[] = '<div class="bevel">';
		        $html[] = $count. '.';
		        $html[] = '</div>';
		        $html[] = '</div>';
		        $html[] = '<div class="text">';
		        
		        $html[] = '<div class="bevel" style="float: left; margin-left: -8px;">';
		        $html[] = '<div style="margin-top: 2px; margin-left: 4px">'.$competence->get_title().'</div>';
		        $html[] = '</div>';
		        $html[] = '<div class="bevel" style="text-align: right;">';
		        $html[] = '<img src="'. Theme :: get_common_image_path() . 'content_object/competence.png' .'" alt=""/>';
		        $html[] = '<div class="clear"></div>';
		        $html[] = '</div>';
		        
		        $html[] = '</div>';
		        $html[] = '<div class="clear"></div>';
		        $html[] = '</div>';
		        $html[] = '<div class="answer">';
		        
	            $html[] = '<div class="description" style="background-color: #fff;">';
	            $html[] = $competence->get_description();
	            $html[] = '<div class="clear"></div>';
	            $html[] = '</div>';
	            
	            // Prints of the table header
    			$this->addElement('html', implode("\n", $html));
    	
	            $this->take_peer_assessment($users, $indicators);
	            
	            $html_end[] = '</div>';
		        
		        $html_end[] = '<div class="clear"></div>';
		        
		        $this->addElement('html', implode("\n", $html_end));
		        
            }
		}	

		
        // Prints of the $html array    	
        //echo implode("\n", $html);
    }
    
    
    // ****************************************************		
    // Prints of the list of indicators for each competence
    // ****************************************************
    function take_peer_assessment($users, $indicators)
    {
    	$renderer = $this->defaultRenderer();

    	
    	$table_header[] = '<div style="overflow: auto;">';
    	$table_header[] = '<table class="data_table take_assessment">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th></th>';
        
        foreach ($users as $user)
        {
        	$user_id = $user->get_user();     	
           	$selected_user = $this->get_parent()->get_user($user_id);
           	$full_user_name = $selected_user->get_firstname() .' '. $selected_user->get_lastname();
            $table_header[] = '<th>' . $full_user_name . '</th>';
        }
        
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
    	
    	// Prints of the table header
    	$this->addElement('html', implode("\n", $table_header));
    	
    	// Prints of a table row with properties foreach indicator
        foreach($indicators as $indicator)
        {
			unset($group);
            //$group[] = $this->createElement('image', null, Theme :: get_common_image_path() . 'content_object/indicator.png');
			$group[] = $this->createElement('static', null, null, $indicator->get_title());
                		

            // Retrieve criteria
            $criteria = $this->get_parent()->get_peer_assessment_page_criterias_via_indicator($this->get_parent()->get_peer_assessment(), $indicator);	            	
            $criteria_overview = $criteria;
            	
            $criteria_scores = array();
            $criteria_scores[0] = Translation :: get('SelectScore');		            	
            
            foreach($criteria as $unserialize)
            {
            	$criteria_score = $unserialize->get_options();
            	foreach($criteria_score as $score)
            	{
            		$criteria_scores[] = $score->get_score();
            	}
            }
        

            // Retrieve results
            $results = new PeerAssessmentPublicationResults();
        	$result = $results->get_data_manager()->retrieve_peer_assessment_publication_result($indicator->get_id());
        	
        	foreach($users as $user)
        	{
        		$indicator_id = $indicator->get_id();
        		$user_id = $user->get_user();
        						
				$group[] = $this->createElement('select', 'select[c'.$competence_id.'i'.$indicator_id.'u'.$user_id.']', '', $criteria_scores);	
        	}

                $this->addGroup($group, 'options_', null, '', false);
   		}
 
	    $renderer->setElementTemplate('<tr id="options_">{element}</tr>', 'options_');
	    $renderer->setGroupElementTemplate('<td>{element}</td>', 'options_');
            
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $table_footer[] = '</div>';
        
        // Prints of the table footer
        $this->addElement('html', implode("\n", $table_footer));


        return implode("\n", $html);
    }
    
}
?>