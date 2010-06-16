<?php
/*
 *	@author Nick Van Loocke
 */

class CompetencesPeerAssessmentResultViewerWizardPage extends PeerAssessmentResultViewerWizardPage
{	
	private $page_number;
	
	function CompetencesPeerAssessmentResultViewerWizardPage($name, $parent, $page_number)
	{
		parent :: PeerAssessmentResultViewerWizardPage($name, $parent);
        $this->page_number = $page_number;
	}
	
	function buildForm()
    {
    	//*******
    	// Values
    	//*******
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
            if($date_message == 'Always open')
            {
            	$html[] = '<div style="float: right; margin-top: -30px;">'.$date_message.'</div>';
            }
            else
            {
            	$html[] = '<div style="float: right; margin-top: -15px;">'.$date_message.'</div>';
            }
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
        }
			
			
		// Retrieve competences
        $competences = $this->get_parent()->get_peer_assessment_page_competences($this->get_parent()->get_peer_assessment());

        $count = 0;
        
       	foreach($competences as $competence)
        {
        	$count_no_indicators = 0;
        	
          	if($count > 0)
            {
            	unset($html);
            }

            // Retrieve indicators
            $indicators = $this->get_parent()->get_peer_assessment_page_indicators_via_competence($this->get_parent()->get_peer_assessment(), $competence);
            	
            if($indicators == null)
            {
            	$count_no_indicators++;
            }
	        elseif(sizeof($indicators) > 0)
            {
            	$count++;
            		
            	$html[] = '<br/>';
            	$html[] = '</div>';
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
	    	
				$this->result_peer_assessment($users, $indicators, $competence, $publication_id, $competences);
 				$result_competence = $this->result($users, $indicators, $competence, $publication_id);
				$total_result += $result_competence;
				$number_of_indicators += sizeof($indicators);
			        
			    $html_end[] = '<div class="clear"></div>';
			        
			  	$this->addElement('html', implode("\n", $html_end));
            } 
        } 
        	
        
    	if($count_no_indicators < sizeof($competences))
        {
	        if($total_result == 0)
	        {
		        $result = Translation :: get('NoScore');
				$pa_factor = Translation :: get('NoScore');
				$pa_factor_after_correction = Translation :: get('NoScore');
	        }
	        else
	        {
		        // Total
		        $result = round($total_result / $number_of_indicators, 2);
				// Get the peer assessment factor
				$pa_factor = $this->pa_factor($competences, $users, $publication_id);
				// Get the peer assessment factor after correction
				$pa_factor_after_correction = $this->pa_factor_after_correction($competences, $users, $publication_id);	
	        }
	        
	        // Total results
        	$this->result_peer_assessment_level($result, $pa_factor, $pa_factor_after_correction, $competences);
        	        
	        // Criteria overview
        	$this->criteria_overview($publication_id);	
	        
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
		$assessment_div[] = '</div>';
		$assessment_div[] = '</div>';
		$this->addElement('html', implode("\n", $assessment_div));
    }
    
    
    // ********************************
    // Prints of the result of one user
    // ********************************
    function result_peer_assessment($users, $indicators, $competence, $publication_id, $competences)
    {  	
    	$renderer = $this->defaultRenderer();
    	
    	$table_header[] = '<div style="overflow: auto;">';
    	$table_header[] = '<table class="data_table take_assessment">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th style="width: 20px;"></th>';
        $table_header[] = '<th style="width: 180px;"></th>';
        
		// Prints of the user name
        $user_id = Session :: get_user_id();    	
        $selected_user = $this->get_parent()->get_user($user_id);
        $full_user_name = $selected_user->get_firstname() .' '. $selected_user->get_lastname();
        $table_header[] = '<th style="width: 250px;">' . $full_user_name . '</th>';
        
        $table_header[] = '<th style="width: 250px;">' . Translation :: get('Average') . '</th>';
        $table_header[] = '<th>' . Translation :: get('AverageAfterCorrection') . '</th>';
        
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
	            $criteria_scores[] = $score->get_score();
	        }
        

            // Retrieve results
            $results = new PeerAssessmentPublicationResults();
        	$result = $results->get_data_manager()->retrieve_peer_assessment_publication_result($indicator->get_id());
        	
			// id's
        	$publication_id = Request :: get("peer_assessment_publication");
        	$competence_id = $competence->get_id();
        	$indicator_id = $indicator->get_id();
        	$user_id = Session :: get_user_id();
        	$graded_user_id = Session :: get_user_id();
        					
        	// Get values given to this user 
        	$score_user = $this->score_user($users, $indicator, $competence, $publication_id);
			$group[] = $this->createElement('static', null, null, $score_user);
			
			
			if($score_user == 'No score')
			{
				$average = Translation :: get('NoScore');
				$average_after_correction = Translation :: get('NoScore');
			}
			else
			{
				// Get average
				$average = $this->average($users, $indicator, $competence, $publication_id);
				// Get average after correction
				$average_after_correction = $this->average_after_correction($users, $indicator, $competence, $publication_id, $competences);
			}
			
			$group[] = $this->createElement('static', null, null, $average);
			$group[] = $this->createElement('static', null, null, $average_after_correction);
			
				
			// Show the values that already has been submitted
			//$publication_result = $this->get_parent()->get_peer_assessment_publication_result($publication_id, $competence_id, $indicator_id, $user_id, $graded_user_id);
		
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
    
    
    // **********	
    // Score user
    // **********
    function score_user($users, $indicator, $competence, $publication_id, $count_indicators)
    {
    	$count = 0;    	
    	$all_filled_out = 0;
    	
    	
    	foreach($users as $user)
    	{
	    	if(sizeof($users) > 3)
	    	{
	    		// Only the scores of the fellow users are used
	    		$you = Session :: get_user_id();
	    		if($user->get_user() != $you)
	    		{    		
		    		$peer_assessment_publication = new PeerAssessmentPublication();
	    			$user_score = $peer_assessment_publication->get_data_manager()->retrieve_peer_assessment_publication_result($publication_id, $competence->get_id(), $indicator->get_id(), $user->get_user(), Session :: get_user_id());
	    			
	    			if($user_score != null)
	    			{
	    				$all_filled_out++;
	    				
		    			if($user_score->get_score() == 0)
		    			{
		    				//Not all criterias are filled in
		    				$error[] = '<div class="clear"></div>';
				        	$error[] = '<div class="error-message">';
				        	$error[] = Translation :: get('NotAllScoresHaveBeenSubmitted');
				        	$error[] = '<div class="close_message" id="closeMessage"></div>';
				        	$error[] = '</div>';
				        	
				        	echo implode("\n", $error);
				        	$count = Translation :: get('NoScore');
		    			}
	    				else
	    				{
	    					$value = $this->score_value($user_score->get_score(), $publication_id);		    						
	    					$count += $value;
	    				}
	    			}
	    		}
	    	}
	    	else
	    	{
	    		// All scores are used (even the ones you give to yourself)	    		
	    		$peer_assessment_publication = new PeerAssessmentPublication();
    			$user_score = $peer_assessment_publication->get_data_manager()->retrieve_peer_assessment_publication_result($publication_id, $competence->get_id(), $indicator->get_id(), $user->get_user(), Session :: get_user_id());   			
    			
    			if($user_score != null)
	    		{
	    			$all_filled_out++;
	    			
	    			if($user_score->get_score() == 0)
	    			{
	    				//Not all criterias are filled in
	    				$error[] = '<div class="clear"></div>';
			        	$error[] = '<div class="error-message">';
			        	$error[] = Translation :: get('NotAllScoresHaveBeenSubmitted');
			        	$error[] = '<div class="close_message" id="closeMessage"></div>';
			        	$error[] = '</div>';
			        	
			        	echo implode("\n", $error);
			        	$count = Translation :: get('NoScore');
	    			}
	    			else
	    			{
	    				$value = $this->score_value($user_score->get_score(), $publication_id);	
	    				$count += $value;
	    			}
	    			
	    			
	    		}
	    	}
    	}
    	
    	if($all_filled_out == sizeof($users))
    	{
    		if($count != 'No score')
    		{
    			$count = $count / sizeof($users);
    			$count = round($count, 2);
    		}
    	}
    	else
    	{
    		$count = Translation :: get('NoScore');

    		/*$error[] = '<div class="clear"></div>';
        	$error[] = '<div class="error-message">';
        	$error[] = Translation :: get('NotAllScoresHaveBeenSubmitted');
        	$error[] = '<div class="close_message" id="closeMessage"></div>';
        	$error[] = '</div>';
        	
        	echo implode("\n", $error);*/
    	}
    	
    	return $count;
    }
    
    
	// ************************************	
    // Gives back the value of the score id
    // ************************************
    function score_value($score_id, $publication_id)
    {   
    	// Retrieve peer assessment_publication
       	$peer_assessment_publication = new PeerAssessmentPublication();
    	$publication = $peer_assessment_publication->get_data_manager()->retrieve_peer_assessment_publication($publication_id);
    	
    	// Criteria id
		$criteria_id = $publication->get_criteria_content_object_id();
		
		// Retrieve criteria            
		$criteria_overview = $this->get_parent()->get_peer_assessment_page_criteria($this->get_parent()->get_peer_assessment(), $criteria_id);
		
		// Criteria options
        $criteria_options = $criteria_overview->get_options();	
        if($criteria_options[$score_id - 1] != null)
        {
        	$value = $criteria_options[$score_id - 1]->get_score();
        }
        
        return $value;		
    }
    
    
	// *******	
    // Average
    // *******
    function average($users, $indicator, $competence, $publication_id)
    {
    	$count = 0;

    	foreach($users as $user)
    	{
	    	if(sizeof($users) > 3)
	    	{
	    		// Only the scores of the fellow users are used
	    		$you = Session :: get_user_id();
	    		if($user->get_user() != $you)
	    		{    		
		    		$conditions[] = new EqualityCondition(PeerAssessmentPublicationResults :: PROPERTY_PUBLICATION_ID, $publication_id);
					$conditions[] = new EqualityCondition(PeerAssessmentPublicationResults :: PROPERTY_COMPETENCE_ID, $competence->get_id());
					$conditions[] = new EqualityCondition(PeerAssessmentPublicationResults :: PROPERTY_INDICATOR_ID, $indicator->get_id());
					$conditions[] = new EqualityCondition(PeerAssessmentPublicationResults :: PROPERTY_USER_ID, $user->get_user());
					$condition = new AndCondition($conditions);
					
					$publications = PeerAssessmentDataManager :: get_instance()->retrieve_peer_assessment_publication_results($condition);
	
			        while ($publication = $publications->next_result())
			        {
			            $value = $this->score_value($publication->get_score(), $publication_id);
			            $count += $value;
			        }
	    		}
	    	}
	    	else
	    	{
	    		// All scores are used (even the ones you give to yourself)	    		
	    		$conditions[] = new EqualityCondition(PeerAssessmentPublicationResults :: PROPERTY_PUBLICATION_ID, $publication_id);
				$conditions[] = new EqualityCondition(PeerAssessmentPublicationResults :: PROPERTY_COMPETENCE_ID, $competence->get_id());
				$conditions[] = new EqualityCondition(PeerAssessmentPublicationResults :: PROPERTY_INDICATOR_ID, $indicator->get_id());
				$conditions[] = new EqualityCondition(PeerAssessmentPublicationResults :: PROPERTY_USER_ID, $user->get_user());
				$condition = new AndCondition($conditions);
				
				$publications = PeerAssessmentDataManager :: get_instance()->retrieve_peer_assessment_publication_results($condition);
				
		        while ($publication = $publications->next_result())
		        {		        	
		            $value = $this->score_value($publication->get_score(), $publication_id);
		            $count += $value;
		        }
	    	}
    	}
    	$average = round($count / sizeof($users), 2);
    	return $average;
    }
    
    
	// ************************	
    // Average after correction
    // ************************
    function average_after_correction($users, $indicator, $competence, $publication_id, $competences)
    {
 		$number_of_users = sizeof($users);
 		
    	if($number_of_users > 2)
	    {
	    	foreach($users as $user)
	    	{		    	
	    		$conditions[] = new EqualityCondition(PeerAssessmentPublicationResults :: PROPERTY_PUBLICATION_ID, $publication_id);
				$conditions[] = new EqualityCondition(PeerAssessmentPublicationResults :: PROPERTY_COMPETENCE_ID, $competence->get_id());
				$conditions[] = new EqualityCondition(PeerAssessmentPublicationResults :: PROPERTY_INDICATOR_ID, $indicator->get_id());
				$conditions[] = new EqualityCondition(PeerAssessmentPublicationResults :: PROPERTY_USER_ID, $user->get_user());
				$condition = new AndCondition($conditions);
	    		
    			$publications = PeerAssessmentDataManager :: get_instance()->retrieve_peer_assessment_publication_results($condition);

	    		while ($publication = $publications->next_result())
		        {
		            $value = $this->score_value($publication->get_score(), $publication_id);
		           	$scores[] = $value;  
		        } 
		        	
	    	}
	    	
     		foreach($competences as $competence)
 			{
 				$indicators = $this->get_parent()->get_peer_assessment_page_indicators_via_competence($this->get_parent()->get_peer_assessment(), $competence);
	    		$number_of_indicators = sizeof($indicators);
	    		$number_of_all_indicators = $number_of_all_indicators + $number_of_indicators;
 			}
	    	
			for($i = 0; $i < $number_of_all_indicators; $i++)
			{
				$count_same = 0;
				$array = $scores[$i];
				
				for($j = 0; $j < $number_of_all_indicators; $j++)
				{
					if($scores[$i] == $scores[$j])
					{
						$count_same++;
					}					
				}
			
				if($count_same != 1)
				{
					// New array with the deleted values, those that only once is given to a user for each indicator are deleted
					$new_values[] = $scores[$i];
				}
			}
			
			foreach($new_values as $new_value)
			{
				$count += $new_value;
			}
			$count = round($count/sizeof($users), 2);
	    }
    	else
    	{
    		$count += $this->average($users, $indicator, $competence, $publication_id);
    	}
    	return $count;
    }
   
    
    
    
    // ************	
    // Total result
    // ************
    function result($users, $indicators, $competence, $publication_id)
    {
		foreach($indicators as $indicator)
        {      					
        	$score_user = $this->score_user($users, $indicator, $competence, $publication_id);
        	$result += $score_user; 
   		}
   		return $result;
    }
    
      
    // **********************	
    // Peer assessment factor
    // **********************
    function pa_factor($competences, $users, $publication_id)
    {
    	// All the values given to one user
    	foreach($users as $user)
		{		
    		if($user->get_user() == Session :: get_user_id())
    		{
	    		$conditions[] = new EqualityCondition(PeerAssessmentPublicationResults :: PROPERTY_PUBLICATION_ID, $publication_id);
				$conditions[] = new EqualityCondition(PeerAssessmentPublicationResults :: PROPERTY_GRADED_USER_ID, $user->get_user());
				$condition = new AndCondition($conditions);
				
				$publications = PeerAssessmentDataManager :: get_instance()->retrieve_peer_assessment_publication_results($condition);
	
		        while ($publication = $publications->next_result())
		        {
		        	$value = $this->score_value($publication->get_score(), $publication_id);
					$count += $value;
		        }
    		}
    	}
    	
    	// Number of users
    	$number_of_users = sizeof($users);
    	// Number of indicators
    	$number_of_all_indicators = 0;
    	foreach($competences as $competence)
    	{
        	$number_of_indicators = sizeof($this->get_parent()->get_peer_assessment_page_indicators_via_competence($this->get_parent()->get_peer_assessment(), $competence));
    		$number_of_all_indicators = $number_of_all_indicators + $number_of_indicators;
    	}
    	// Number of criteria
       	$criteria_id = PeerAssessmentDataManager :: get_instance()->retrieve_peer_assessment_publication($publication_id)->get_criteria_content_object_id();        
		$criterias = $this->get_parent()->get_peer_assessment_page_criteria($this->get_parent()->get_peer_assessment(), $criteria_id)->get_options();

        foreach($criterias as $score_and_description)
        {
            $number_of_criteria++;
        }

        
    	$pa_factor = round(($count / ((($number_of_users * $number_of_all_indicators) * $number_of_criteria) * 2)), 2);
    	return $pa_factor;   	
    }
    
    
	// ***************************************	
    // Peer assessment factor after correction
    // ***************************************
    function pa_factor_after_correction($competences, $users, $publication_id)
    {
    	$number_of_users = sizeof($users);
    	if($number_of_users > 2)
    	{
			foreach($competences as $competence)
			{
				$indicators = $this->get_parent()->get_peer_assessment_page_indicators_via_competence($this->get_parent()->get_peer_assessment(), $competence);
	    		$number_of_indicators = sizeof($indicators);
	    		$number_of_all_indicators = $number_of_all_indicators + $number_of_indicators;
	    		
				foreach($indicators as $indicator)
				{
					foreach($users as $user)
					{
			    		$conditions[] = new EqualityCondition(PeerAssessmentPublicationResults :: PROPERTY_PUBLICATION_ID, $publication_id);
			    		$conditions[] = new EqualityCondition(PeerAssessmentPublicationResults :: PROPERTY_GRADED_USER_ID, $user->get_user());
						$condition = new AndCondition($conditions);
			    		
		    			$publications = PeerAssessmentDataManager :: get_instance()->retrieve_peer_assessment_publication_results($condition);
		
			    		while ($publication = $publications->next_result())
				        {					        	
				        	$value = $this->score_value($publication->get_score(), $publication_id);
				        	$all_scores[] = $value; 
				        } 		
					}
				}
			}
			//Split the objects in an array also in arrays
			$chunks[] = array_chunk($all_scores, $number_of_all_indicators);
			foreach($chunks as $chunk)
			{
				for($i = 0; $i < $number_of_all_indicators; $i++)
				{
					$array = $chunk[$i];
					
					for($j = 0; $j < $number_of_all_indicators; $j++)
					{
						$count_same = 0;
						foreach($array as $array_value)
						{
							if($array[$j] == $array_value)
							{
								$count_same++;
							}
						}
						
						if($count_same != 1)
						{
							// New array with the deleted values, those that only once is given to a user for each indicator are deleted
							$new_values[] = $array[$j];
						}
					}
				}
			}
			
			foreach($new_values as $new_value)
			{
				$count = $count + $new_value;
			}
			
			// Number of criteria
	       	$criteria_id = PeerAssessmentDataManager :: get_instance()->retrieve_peer_assessment_publication($publication_id)->get_criteria_content_object_id();        
			$criterias = $this->get_parent()->get_peer_assessment_page_criteria($this->get_parent()->get_peer_assessment(), $criteria_id)->get_options();
	
	        foreach($criterias as $score_and_description)
	        {
	            $number_of_criteria++;
	        }
			
			$pa_factor_after_correction = round(($count / ((($number_of_users * $number_of_all_indicators) * $number_of_criteria) * 2)), 2);	
    	}
    	else
    	{
    		foreach($competences as $competence)
			{
				$indicators = $this->get_parent()->get_peer_assessment_page_indicators_via_competence($this->get_parent()->get_peer_assessment(), $competence);
	    		$number_of_indicators = sizeof($indicators);
	    		$number_of_all_indicators = $number_of_all_indicators + $number_of_indicators;
			}
			
			$pa_factor_after_correction = $this->pa_factor($competences, $users, $publication_id);
    	}
		
    	
    	return $pa_factor_after_correction;
    }
    
    
	// ********************************	
    // Results on peer assessment level
    // ********************************
    function result_peer_assessment_level($result, $pa_factor, $pa_factor_after_correction, $competences)
    {
    	$html[] = '<br/>';
    	$html[] = '<div style="margin-left: -50px;">';
    	//$html[] = '<div style="float: left; width: 100%;">';
		//$html[] = '<div class="question">';
        $html[] = '<div class="title">';
        $html[] = '<div class="text">';
	        
        $html[] = '<div class="bevel" style="float: left; margin-left: -8px;">';
        $html[] = '<div style="margin-top: 2px; margin-left: 4px">'.Translation :: get('Result').'</div>';
        $html[] = '</div>';
        $html[] = '<div class="bevel" style="text-align: right;">';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
	        
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = '<div class="answer">';
        
        $html[] = '<div class="description" style="background-color: #fff;">';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
    	
    
            
        // Prints of the beginning of the table
    	$this->addElement('html', implode("\n", $html));
    	
    	$table_header[] = '<div style="overflow: auto; width: 100%; float: left;">';
    	$table_header[] = '<table class="data_table take_assessment">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th style="width: 20px;"></th>';
        $table_header[] = '<th style="width: 180px;"></th>';
        
        // Prints of the user name
        $user_id = Session :: get_user_id();    	
        $selected_user = $this->get_parent()->get_user($user_id);
        $full_user_name = $selected_user->get_firstname() .' '. $selected_user->get_lastname();
        
        $table_header[] = '<th style="width: 250px;">' . $full_user_name . '</th>';     
        $table_header[] = '<th style="width: 250px;">' . Translation :: get('PAFactor') . '</th>';
        $table_header[] = '<th>' . Translation :: get('PAFactorAfterCorrection') . '</th>';
        
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
    	
    	// Prints of the table header
    	$this->addElement('html', implode("\n", $table_header));
    	
    	$table_values[] = '<tr>';
    	$table_values[] = '<td></td>';
    	$table_values[] = '<td></td>';
    	$table_values[] = '<td>'.$result.'</td>';
    	$table_values[] = '<td>'.$pa_factor.'</td>';
    	$table_values[] = '<td>'.$pa_factor_after_correction.'</td>';
    	$table_values[] = '<tr>';
    	
    	// Prints of the table values
    	$this->addElement('html', implode("\n", $table_values));
    	
    	$table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $table_footer[] = '</div>';
        
        // Prints of the table footer
        $this->addElement('html', implode("\n", $table_footer));

        $new_number_of_competences = (sizeof($competences) - 1);
		for($i = 0; $i < $new_number_of_competences; $i++)
		{
			$html_end[] = '</div>';	
		}
		      
	    $html_end[] = '<div class="clear"></div>';
	        
	    // Prints of the ending of the table
	  	$this->addElement('html', implode("\n", $html_end));
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
}
?>