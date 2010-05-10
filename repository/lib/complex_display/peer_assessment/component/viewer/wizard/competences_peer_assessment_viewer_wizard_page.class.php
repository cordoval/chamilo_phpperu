<?php
/*
 *	@author Nick Van Loocke
 */

class CompetencesPeerAssessmentViewerWizardPage extends PeerAssessmentViewerWizardPage
{
    private $page_number;

    function CompetencesPeerAssessmentViewerWizardPage($name, $parent, $page_number)
    {
        parent :: PeerAssessmentViewerWizardPage($name, $parent);
        $this->page_number = $page_number;
    }

    function buildForm()
    {
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
        
    	// Groups
        if($count_groups != 0)
        {
           	foreach($groups as $group)
           	{
           		$group_id = $group->get_group_id();     	
           		$selected_group = $this->get_parent()->get_group($group_id);
           		$group_name[] = $selected_group->get_name();
           			
           		/*$items++;
           		if($count_groups > $items)
           		{
           			$group_name[] = ','; 
           			//$html[] = ',';
           		}*/
           	} 
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
		
		
		// Retrieve competences
        $competences = $this->get_parent()->get_peer_assessment_page_competences($this->get_parent()->get_peer_assessment());

        $count = 0;
        
	        
        foreach($competences as $competence)
      	{     		
      		$noInidcators = true;
      		$countNoIndicators = 0;
      		
            if($count > 0)
            {
            	unset($html);
            }

            // Retrieve indicators
            $indicators = $this->get_parent()->get_peer_assessment_page_indicators_via_competence($this->get_parent()->get_peer_assessment(), $competence);
            
            if($indicators == null)
            {
            	$countNoIndicators++;
            }
            elseif(sizeof($indicators) > 0)
            {
            	$count++;
            	$noIndicators = false;
            	
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
    	
	            $this->take_peer_assessment($users, $indicators, $competence, $publication_id);
	            
	            $html_end[] = '</div>';
		        
		        $html_end[] = '<div class="clear"></div>';
		        
		        $this->addElement('html', implode("\n", $html_end));

            }
        }      
          
        if($countNoIndicators < sizeof($competences))
        {
        	$this->criteria_overview($publication_id);	 
			$this->submit();
			
			$assessment_div[] = '</div>';
        }
        else
        {
        	$error[] = '<div class="clear"></div>';
        	$error[] = '<div class="error-message">';
        	$error[] = Translation :: get('NoIndicatorsInThePeerAssessment');
        	$error[] = '<div class="close_message" id="closeMessage"></div>';
        	$error[] = '</div>';
        	
        	$this->addElement('html', implode("\n", $error));
        }
			
		
		$assessment_div[] = '</div>';
		$this->addElement('html', implode("\n", $assessment_div));
    }
    
    
    // ****************************************************		
    // Prints of the list of indicators for each competence
    // ****************************************************
    function take_peer_assessment($users, $indicators, $competence, $publication_id)
    {
    	$renderer = $this->defaultRenderer();
    	
    	$table_header[] = '<div style="overflow: auto;">';
    	$table_header[] = '<table class="data_table take_assessment">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th style="width: 20px;"></th>';
        $table_header[] = '<th style="width: 180px;"></th>';
        
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
			$group[] = $this->createElement('static', null, null, '<img src="'. Theme :: get_common_image_path() . 'content_object/indicator.png' .'" alt=""/>');
			$group[] = $this->createElement('static', null, null, $indicator->get_title());
                		

			// Retrieve peer assessment_publication
	       	$peer_assessment_publication = new PeerAssessmentPublication();
	    	$publication = $peer_assessment_publication->get_data_manager()->retrieve_peer_assessment_publication($publication_id);
	    	
	    	// Criteria id
			$criteria_id = $publication->get_criteria_content_object_id();
			
			// Retrieve criteria            
			$criteria_overview = $this->get_parent()->get_peer_assessment_page_criteria($this->get_parent()->get_peer_assessment(), $criteria_id);
			
			
			$criteria_scores = array();
            $criteria_scores[0] = Translation :: get('SelectScore');
			
	        $criteria_options = $criteria_overview->get_options();
	        foreach($criteria_options as $score)
	        {
	            $criteria_scores[] = $score->get_description();
	        }
        

            // Retrieve results
            $results = new PeerAssessmentPublicationResults();
        	$result = $results->get_data_manager()->retrieve_peer_assessment_publication_result($indicator->get_id());
        	
        	foreach($users as $user)
        	{
        		$publication_id = Request :: get("peer_assessment_publication");
        		$competence_id = $competence->get_id();
        		$indicator_id = $indicator->get_id();
        		$user_id = Session :: get_user_id();
        		$graded_user_id = $user->get_user();
        						
				$group[] = $select = $this->createElement('select', 'select[c'.$competence_id.'i'.$indicator_id.'u'.$graded_user_id.']', '', $criteria_scores);
					
				// Show the values that already has been submitted
				$publication_result = $this->get_parent()->get_peer_assessment_publication_result($publication_id, $competence_id, $indicator_id, $user_id, $graded_user_id);

				if($publication_result != null)
				{
					$select->setSelected($publication_result->get_score());
				}
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
    }
    
    
    // *******************************	
    // Prints of the criteria overview
    // *******************************
    function criteria_overview($publication_id)
    {   	
    	// Retrieve peer assessment_publication
       	$peer_assessment_publication = new PeerAssessmentPublication();
    	$publication = $peer_assessment_publication->get_data_manager()->retrieve_peer_assessment_publication($publication_id);
    	
    	// Criteria id
		$criteria_id = $publication->get_criteria_content_object_id();
		
		// Retrieve criteria            
		$criteria_overview = $this->get_parent()->get_peer_assessment_page_criteria($this->get_parent()->get_peer_assessment(), $criteria_id);
		
		// Overview of the criteria (score and description)
        $overview[] = '<div style="float: left;">';
        $overview[] = '<br/>'. Translation :: get('OverviewOfTheCriteria');
        $overview[] = '<ul>';

        $criteria_options = $criteria_overview->get_options();
        foreach($criteria_options as $score_and_description)
        {
            $overview[] = '<li>'. Translation :: get('CriteriaScore') .': <b>'. $score_and_description->get_score() .'</b> |  '. Translation :: get('CriteriaDescription') .': <b>'. $score_and_description->get_description() .'</b></li>';
        }

        $overview[] = '</ul>';
        $overview[] = '</div>';
		
    	// Prints of the overview of the criteria
		$this->addElement('html', implode("\n", $overview));		
    }
    
    
    // ***************************	
    // Prints of the submit button
    // ***************************
    function submit()
    {
    	// Submit button
		$button[] = '<div style="float: right; margin-top: 15px">';
        $button[] = $this->createElement('style_submit_button', $this->getButtonName('submit'), Translation :: get('Submit'), array('class' => 'positive'))->toHtml();
        $button[] = '</div>';
        
        // Prints of the submit button
		$this->addElement('html', implode("\n", $button));
		
		// Process: create, update, ... the peer assessment
		$this->addAction('process', new PeerAssessmentViewerWizardProcess($this));
    }
    
}
?>