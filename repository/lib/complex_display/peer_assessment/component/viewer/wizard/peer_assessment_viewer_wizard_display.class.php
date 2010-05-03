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
        $this->parent = $parent;
    }

    /**
     * Displays the HTML-code of a page in the wizard
     * @param HTML_Quickform_Page $page The page to display.
     */
    function _renderForm($current_page)
    {   	
        $html = array();
        $publication_id = Request :: get('peer_assessment_publication');
        $competence_id = Request :: get('competence');
        $indicator_id = Request :: get('indicator');
        $feedback = Request :: get('feedback');
        $saved = Request :: get('saved');
        
        $type = Request :: get('go');
        
        
        // Content object id
	    $peer_assessment_id = $this->parent->get_peer_assessment()->get_id();
	
        // Publication object     	
	    $peer_assessment_publication = $this->parent->get_peer_assessment_publication($publication_id);
        
	    // Groups
        $groups = $this->parent->get_peer_assessment_publication_groups($publication_id)->as_array();
        $count_groups = sizeof($this->parent->get_peer_assessment_publication_groups($publication_id)->as_array());
			          	    
	    // Users
		$users = $this->parent->get_peer_assessment_publication_users($publication_id)->as_array();   
		$count_users = sizeof($this->parent->get_peer_assessment_publication_users($publication_id)->as_array());
	    
		
	    // **********************************************************************		
        // Prints of the list of competences (with the users, groups, dates, ...)
        // **********************************************************************
        if($competence_id == null)
        {
        	dump('test');
        	dump($_POST);
        	
        	//If there are no competence objects in the selected peer assessment
	        if($current_page->get_page_number() == 0)
	        {
	        	$html[] = '<div class="clear"></div>';
        		$html[] = '<div class="error-message">';
        		$html[] = Translation :: get('NoCompetencesInThePeerAssessment');
        		$html[] = '<div class="close_message" id="closeMessage"></div>';
        		$html[] = '</div>';
        		echo implode("\n", $html);
	        }
	        //If there are competence objects in the selected peer assessment
	        elseif ($current_page->get_page_number() == 1)
	        {
	            $html[] = '<div class="assessment">';
	            
	            // Peer assessment title en description
	            $html[] = '<h2>' . $this->parent->get_peer_assessment()->get_title() . '</h2>';
	            
		        if ($this->parent->get_peer_assessment()->has_description())
		        {
		            $html[] = '<div class="description">';
		            $html[] = $this->parent->get_peer_assessment()->get_description();
		            $html[] = '<div class="clear"></div>';
		            $html[] = '</div>';
		        }	        

	        	// Get groups
		        $groups = $this->parent->get_peer_assessment_publication_groups($publication_id)->as_array();
		        $count_groups = sizeof($this->parent->get_peer_assessment_publication_groups($publication_id)->as_array());
            	
		        // Get users
				$users = $this->parent->get_peer_assessment_publication_users($publication_id)->as_array();   
		        $count_users = sizeof($this->parent->get_peer_assessment_publication_users($publication_id)->as_array());	           
		        
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
					// Header of the table          
		            $html[] = '<br /><h3>' . Translation :: get('Competence') . '</h3>';
			        $html[] = '<table class="data_table">';
			        $html[] = '<thead>';
			        $html[] = '<tr>';
			        $html[] = '<th>'. Translation :: get('Type') .'</th>';
			        $html[] = '<th>' . Translation :: get('Title') . '</th>';
			        $html[] = '<th>' . Translation :: get('From') . ' - ' . Translation :: get('To') . '</th>';
			        $html[] = '<th>' . Translation :: get('Groups') . '</th>';
			        $html[] = '<th>' . Translation :: get('Users') . '</th>';
			        $html[] = '<th class="numeric">' . Translation :: get('Finished') . '</th>';
			        $html[] = '<th class="action"></th>';
			        $html[] = '</tr>';
			        $html[] = '</thead>';
			        $html[] = '<tbody>';
		        	
					// Retrieve competences
			        $competences = $this->parent->get_peer_assessment_page_competences($this->parent->get_peer_assessment());
			        
		            foreach($competences as $competence)
		            {
			            	$count = 0;
			            	
			            	// Hyperlinks to the list of indicator objects
			            	$url_indicator_list_take = 'run.php?go=take_publication&application=peer_assessment&peer_assessment_publication=' . $publication_id . '&competence=' .$competence->get_id();
			            	$url_indicator_list_results = 'run.php?go=view_publication_results&application=peer_assessment&peer_assessment_publication=' . $publication_id . '&competence=' .$competence->get_id();
			            	  
			            	//Take peer assessment or view peer assessment results have different url's and images
			            	if($type == 'take_publication')
		        			{   
		        				// Title link
			            		$title = '<a href="'. $url_indicator_list_take .'">'.$competence->get_title().'</a>';      
			            		// Image link	
			            		$take_peer_assessment = '<a href="'. $url_indicator_list_take .'"><img src="' . Theme :: get_common_image_path() . 'action_next.png' .'" alt=""/></a>';
		        			}
		        			elseif($type == 'view_publication_results')
		        			{
		        				// Title link
			            		$title = '<a href="'. $url_indicator_list_results .'">'.$competence->get_title().'</a>';      
			            		// Image link	
		        				$take_peer_assessment = '<a href="'. $url_indicator_list_results .'"><img src="' . Theme :: get_common_image_path() . 'action_view_results.png' .'" alt=""/></a>';
		        			}            	
			            	
			            	// From date - To date
			            	$from_date = $peer_assessment_publication->get_from_date();
			            	$to_date = $peer_assessment_publication->get_to_date();
			            	
			            	if(($from_date == 0) && ($to_date == 0))
			            	{
			            		$date_message = Translation :: get('AlwaysOpen');
			            	}
			            	elseif($to_date > time())
			            	{
			            		$from_date = DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), $from_date);
			            		$to_date = DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), $to_date);
			            		$date_message = $from_date . ' - ' . $to_date;
			            	}
			            	else
			            	{
			            		$from_date = DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), $from_date);
			            		$to_date = DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), $to_date);
			            		$date_message = $from_date . ' - ' . $to_date;
			            		
			            		            		
			            		if($type == 'take_publication')
			            		{
			            			$title = '<b>'.$competence->get_title().'</b>';
			            			$take_peer_assessment = '<img src="' . Theme :: get_common_image_path() . 'action_next.png' .'" alt=""/>';		            	
			            		}
			            		elseif($type == 'view_publication_results')
			        			{
			        				$title = '<a href="'. $url_indicator_list_results .'">'.$competence->get_title().'</a>';      
			        				$take_peer_assessment = '<a href="'. $url_indicator_list_results .'"><img src="' . Theme :: get_common_image_path() . 'action_view_results.png' .'" alt=""/></a>';
			        			}
			            	}
			            	
			            	$html[] = '<tr>';
			            	$html[] = '<td><img src="'. Theme :: get_common_image_path() . 'content_object/competence.png' .'" alt=""/></td>';
			            	$html[] = '<td>'. $title .'</td>';
			            	$html[] = '<td>'. $date_message .'</td>';
			            	$html[] = '<td>';
		            	
			            	// Groups
			            	if($count_groups != 0)
			            	{
				           		foreach($groups as $group)
				           		{
				           			$group_id = $group->get_group_id();     	
				           			$selected_group = $this->parent->get_group($group_id);
				           			$group_name = $selected_group->get_name();
				           			$html[] = $group_name;
				           			
				           			$items++;
				           			if($count_groups > $items)
				           			{
				           				$html[] = ',';
				           			}
				           		} 
			            	}
			            	else
			            	{
			            		$html[] = Translation :: get('NoGroups');
			            	}
			            	$html[] = '</td>';
			            	$html[] = '<td>';
		            	
		            	
			            	// Users			            				            	
			            	if($count_users != 0)
			            	{
			            		$items = 0;
				            	foreach($users as $user)
				           		{
				           			$user_id = $user->get_user();     	
				           			$selected_user = $this->parent->get_user($user_id);
				           			$full_user_name = $selected_user->get_firstname() .' '. $selected_user->get_lastname();
				           			$html[] = $full_user_name;	
				           			
				           			$items++;
				           			if($count_users > $items)
				           			{
				           				$html[] = ',';
				           			}
				           		}  
			            	}
		 	            	
			            	$html[] = '</td>';
			            	
			            	
			            	//if($competence->isFinished())
			            	//{
			            	//	  $image = 'button_start';
			            	//}
			            	//else
			            	//{
			            		$image = 'button_cancel';
			            	//}
			            	
			            		
			            	$html[] = '<td><img src="' . Theme :: get_common_image_path() . 'buttons/'.$image.'.png' .'" alt="" /></td>';
			            	$html[] = '<td>'. $take_peer_assessment .'</td>';
			            	$html[] = '</tr>';	
				
		            }
	            }
		            
			            
	            $html[] = '</tbody>';
	            $html[] = '</table>';
	            		
	            $html[] = '<br />';
	            $html[] = '</div>';
	
	            $html[] = '<div>';
	            $html[] = $current_page->toHtml();
	            $html[] = '</div>';
	            
	            echo implode("\n", $html);
	        }
        }
    
        // *********************************************************************
        // Prints of the list of indicators (with the user drop down boxes, ...)
        // *********************************************************************
        elseif($competence_id > 0)
        {
        	if($indicator_id == null)
        	{
	        	// Competence object
	        	$competence = RepositoryDataManager :: get_instance()->retrieve_content_object($competence_id);
	        	// Form	
	        	$form = new FormValidator('peer_assessment_publication_mover', 'post');
		        
	        	$html[] = '<div class="assessment">';
	        	
	        	// Competence title and description
	            $html[] = '<h2>' . $competence->get_title() . '</h2>';
	            	            
		        if ($competence->has_description())
		        {
		            $html[] = '<div class="description">';
		            $html[] = $competence->get_description();
		            $html[] = '<div class="clear"></div>';
		            $html[] = '</div>';
		        }
	            $html[] = '<br />';            
	            
	            // There must be atleast one user to take the peer assessment
	            if($count_users > 0)
	            {		        
		            // Retrieve indicators of the selected competence
		            $indicators = $this->parent->get_peer_assessment_page_indicators_via_competence($this->parent->get_peer_assessment(), $competence);
					
		            if(sizeof($indicators) != null)
		            {
	            		// Header table
	            		$html[] = '<h3>' . Translation :: get('Indicator') . '</h3>';            
				        $html[] = '<table class="data_table">';
				        $html[] = '<thead>';
				        $html[] = '<tr>';
				        $html[] = '<th>'. Translation :: get('Type') .'</th>';
				        $html[] = '<th>' . Translation :: get('Title') . '</th>';
				        foreach($users as $user)
				        {
			        		$user_id = $user->get_user();     	
			           		$selected_user = $this->parent->get_user($user_id);
			           		$full_user_name = $selected_user->get_firstname() .' '. $selected_user->get_lastname();
				        	$html[] = '<th>' . $full_user_name . '</th>';
				        }    
				        if($type == 'take_publication')
				        {    
				        	$html[] = '<th class="numeric">' . Translation :: get('Finished') . '</th>';
				        }
				        $html[] = '<th></th>';
				        $html[] = '</tr>';
				        $html[] = '</thead>';
				        $html[] = '<tbody>';
		            			            		

	            		// Prints of a table row with properties foreach indicator
			            foreach($indicators as $indicator)
			            {
			            	$html[] = '<tr>';
			            	$html[] = '<td><img src="'. Theme :: get_common_image_path() . 'content_object/indicator.png' .'" alt=""/></td>';
			            	$html[] = '<td>'.$indicator->get_title().'</td>';
			            	$url_result = 'run.php?go=take_publication&application=peer_assessment&peer_assessment_publication=' . $publication_id . '&competence=' .$competence->get_id() . '&indicator=' .$indicator->get_id();
			            	$url_feedback = 'run.php?go=view_publication_results&application=peer_assessment&peer_assessment_publication=' . $publication_id . '&competence=' .$competence->get_id() . '&indicator=' .$indicator->get_id() .'&feedback=true';
			            	
			            	
			            	// Retrieve criteria
				            $criteria = $this->parent->get_peer_assessment_page_criterias_via_indicator($this->parent->get_peer_assessment(), $indicator);	            	
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
				            
				            // Take peer assessment
			            	if($type == 'take_publication')
			            	{
			            		// Retrieve results
				            	$results = new PeerAssessmentPublicationResults();
					        	$result = $results->get_data_manager()->retrieve_peer_assessment_publication_result($indicator->get_id());
					        	
					        	foreach($users as $user)
					        	{
					        		$html[] = '<td>';
									//$form = new FormValidator('peer_assessment_publication_results', 'post', $url_result);
					        		$current = $current_page->get_criteria($criteria_scores, $user->get_user(), $form)->toHtml();
					        		$current_pages &= $current;	
					        		//$html[] = $current;
							        $html[] = '</td>';
					        	}
					        	  
								
					        	if($result == null)
					        	{
					        		$image = 'button_cancel';
					        	}
					        	else
					        	{
						        	if($result->is_finished())
					            	{
					            		$image = 'button_start';
					            	}
					            	else
					            	{
					            		$image = 'button_cancel';
					            	}	
				            	}	        	
		
				            	$html[] = '<td><img src="' . Theme :: get_common_image_path() . 'buttons/'.$image.'.png' .'" alt="" /></td>';	            	
				            	$html[] = '<td>';
				            	//$html[] = '<a href="'. $url_result  .'"><img src="' . Theme :: get_common_image_path() . 'action_save.png' .'" alt=""/>&nbsp;</a>';
				            	$html[] = '<a href="'. $url_feedback .'"><img src="' . Theme :: get_common_image_path() . 'action_reset.png' .'" alt=""/></a>';
				            	$html[] = '</td>';
				            }
				            
				            // View peer assessment results
				            elseif($type == 'view_publication_results')
				            {
					        	
				            	foreach($users as $user)
					        	{
					        		// Retrieve score
					        		$results = new PeerAssessmentPublicationResults();
					        		$result = $results->get_data_manager()->retrieve_peer_assessment_publication_result_score($indicator->get_id(), $user->get_user());				        	
					        		
					        		if(($result == null) || ($result->get_score()) == 0)
					        		{
					        			$score = Translation :: get('NoScore');
					        		}
					        		else
					        		{
					        			$score = $criteria_scores[$result->get_score()];
					        		}
					        		
					        		$html[] = '<td>';
					        		$html[] = $score;
							        $html[] = '</td>';
					        	} 
					        	$html[] = '<td><a href="'. $url_feedback .'"><img src="' . Theme :: get_common_image_path() . 'action_reset.png' .'" alt=""/></a></td>';
				            }
			            	$html[] = '</tr>';
			            }
			            
				        $html[] = '</tbody>';
			            $html[] = '</table>';
		
			            // Overview of the criteria (score and description)
			            $html[] = '<div style="float: left;">';
			            $html[] = '<br/>'. Translation :: get('OverviewOfTheCriteria');
			            $html[] = '<ul>';
		            	foreach($criteria_overview as $unserialize)
		            	{
		            		$criteria_score = $unserialize->get_options();
		            		foreach($criteria_score as $score_and_description)
		            		{
		            			$html[] = '<li>'. Translation :: get('CriteriaScore') .': <b>'. $score_and_description->get_score() .'</b> |  '. Translation :: get('CriteriaDescription') .': <b>'. $score_and_description->get_description() .'</b></li>';
		            		}
		            	}
			            $html[] = '</ul>';
			            $html[] = '</div>';	       
			            $html[] = '</div>';	            
			            
			
			            $html[] = '<div>';
			            $html[] = $current_page->toHtml();
			            $html[] = '</div>';

			        }	
	            	else
	            	{
	            		// If there are no indicators
	            		$html[] = '<div class="clear"></div>';
		        		$html[] = '<div class="error-message">';
		        		$html[] = Translation :: get('NoIndicatorsInTheCompetence');
		        		$html[] = '<div class="close_message" id="closeMessage"></div>';
		        		$html[] = '</div>';
	            	}	            	            
		            $html[] = '</div>';
	            }
	            else
	            {
	            	// If there are no users
	            	$html[] = '<div class="clear"></div>';
	        		$html[] = '<div class="error-message">';
	        		$html[] = Translation :: get('NoUsersInThePeerAssessment');
	        		$html[] = '<div class="close_message" id="closeMessage"></div>';
        			$html[] = '</div>';
	            }
            }
            elseif($indicator_id > 0)
            {     
            	
            }
            echo implode("\n", $html);
        }
        $this->parent->get_parent()->display_footer();
    }
    
    /*function page_number($current_page)
    {
    	$html[] = '<div>';
        $html[] = $current_page->toHtml();
        $html[] = '</div>';
    	
    	$html[] = '<br />';	
        $html[] = '<div class="assessment">';	
        $html[] = '<br />';	
        
        $html[] = '<div style="width: 100%; text-align: center;">';
        $html[] = $current_page->get_page_number()+2 . ' - ' . $this->parent->get_total() . ' / ' . $this->parent->get_total();
        $html[] = '</div>';
        
        $html[] = '</div>';
        
        return $html;
    }*/
}
?>