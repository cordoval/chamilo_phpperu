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
	
        
        // Prints of the list of competences (with the users, groups, dates, ...)
        if($competence_id == null)
        {
        	
        	//If there are no competence objects in the selected peer assessment
	        if($current_page->get_page_number() == 0)
	        {
	        	$html = array();
	            $html[] = '<div style="width: 100%; text-align: center;">';
	            $html[] = Translation :: get('ThePeerAssessmentHasNoCompetences');
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
	            	$url_indicator_list = 'run.php?go=take_publication&application=peer_assessment&peer_assessment_publication=' . $publication_id . '&competence=' .$competence->get_id();
	            	// Title link
	            	$title = '<a href="'. $url_indicator_list .'">'.$competence->get_title().'</a>';      
	            	// Image link     	
	            	$take_peer_assessment = '<a href="'. $url_indicator_list .'"><img src="' . Theme :: get_common_image_path() . 'action_next.png' .'" alt=""/></a>';
	            		            	
	            	
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
	            		
	            		$title = '<b>'.$competence->get_title().'</b>';
	            		$take_peer_assessment = '<img src="' . Theme :: get_common_image_path() . 'action_next.png' .'" alt=""/>';
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
		           			for($i = 1; $i < $count_groups; $i++)
			           		{
			           			$html[] = ',';
			           			$count_users--;
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
		            	foreach($users as $user)
		           		{
		           			$user_id = $user->get_user();     	
		           			$selected_user = $this->parent->get_user($user_id);
		           			$full_user_name = $selected_user->get_firstname() .' '. $selected_user->get_lastname();
		           			$html[] = $full_user_name;
			           		for($i = 1; $i < $count_users; $i++)
			           		{
			           			$html[] = ',';
			           			$count_users--;
			           		}
		           		}  
	            	}
	            	else
	            	{
	            		$html[] = Translation :: get('NoUsers');
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
	            $html[] = '</tbody>';
	            $html[] = '</table>';
	            
				
		
	            $html[] = '<br />';
	            $html[] = '</div>';
	
	            $html[] = '<div>';
	            $html[] = $current_page->toHtml();
	            $html[] = '</div>';
	
	            $html[] = '<div class="assessment">';
	            $html[] = '<div style="width: 100%; text-align: center;">';
	            $html[] = $current_page->get_page_number() . ' - ' . $this->parent->get_total_competences() . ' / ' . $this->parent->get_total_competences();
	            $html[] = '</div>';
	            $html[] = '</div>';
	
	            echo implode("\n", $html);
	        }
        }
        
        if($competence_id > 0)
        {
        	if($indicator_id == null)
        	{
	        	// Competence object
	        	$competence = RepositoryDataManager :: get_instance()->retrieve_content_object($competence_id);

		        
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
			        $html[] = '<th class="numeric">' . Translation :: get('Finished') . '</th>';
			        $html[] = '<th class="action"></th>';
			        $html[] = '</tr>';
			        $html[] = '</thead>';
			        $html[] = '<tbody>';	        
		
		            // Retrieve indicators of the selected competence
		            $indicators = $this->parent->get_peer_assessment_page_indicators_via_competence($this->parent->get_peer_assessment(), $competence);
					
		            foreach($indicators as $indicator)
		            {
		            	$html[] = '<tr>';
		            	$html[] = '<td><img src="'. Theme :: get_common_image_path() . 'content_object/indicator.png' .'" alt=""/></td>';
		            	$html[] = '<td>'.$indicator->get_title().'</td>';
		            	$url_result = 'run.php?go=take_publication&application=peer_assessment&peer_assessment_publication=' . $publication_id . '&competence=' .$competence->get_id() . '&indicator=' .$indicator->get_id();
		            	
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
	
		            	foreach($users as $user)
			        	{
			        		$html[] = '<td>';
			        		$html[] = $form = new FormValidator('peer_assessment_publication_results', 'post', $url_result);
					    	$current = $current_page->get_criteria($criteria_scores, $user->get_user(), $form)->toHtml();
			        		$current_pages &= $current;
			        		$html[] = $current;
					        $html[] = '</td>';
			        	}    

		            	$results = new PeerAssessmentPublicationResults();
		            	// Retrieve results
			        	$result = $results->get_data_manager()->retrieve_peer_assessment_publication_results($indicator->get_id());
			        	
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
		            	$html[] = '<td><a href="'. $url_result .'"><img src="' . Theme :: get_common_image_path() . 'action_next.png' .'" alt=""/></a></td>';
		            	$html[] = '</tr>';
		            }
		            
		            $html[] = '</tbody>';
		            $html[] = '</table>';
	
		            // Overview of the criteria (score and description)
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
		            
		
		            $html[] = '<div>';
		            $html[] = $current_page->toHtml();
		            $html[] = '</div>';
		
		            $html[] = '<br />';
		
		            $html[] = '<div class="assessment">';
		            $html[] = '<br />';
		
		            $html[] = '<div style="width: 100%; text-align: center;">';
		            $html[] = $current_page->get_page_number() . ' - ' . $this->parent->get_total_competences() . ' / ' . $this->parent->get_total_competences();
		            $html[] = '</div>';
		            $html[] = '</div>';
	            }
	            else
	            {
            		$html[] = '<div style="width: 100%; text-align: center;">';
            		$html[] = Translation :: get('NoUsersInThePeerAssessment');
            		$html[] = '</div>';
	            }
            }
            elseif($indicator_id > 0)
            {     
            	// Create publication results
				$finished = 1;
				
				foreach($users as $user)
            	{
					$results = new PeerAssessmentPublicationResults();
					$results->set_publication_id($publication_id);
					$results->set_competence_id($competence_id);
					$results->set_indicator_id($indicator_id);
					$results->set_user_id(Session :: get_user_id());
					$results->set_graded_user_id($user->get_user());
					$results->set_score(0);//$_POST['criteria_score_of_user_id_'.Session :: get_user_id()]);
					$results->set_finished($finished);
					
					$results->create();
            	}
	            
				/*
				foreach($users as $user)
	        	{
	        		
	        		$current = $current_page->get_criteria($criteria_scores, $user->get_user(), $form);
	        		$current_pages &= $current;	
	        	}
	        	exit();*/
           	
            	//'run.php?go=take_publication&application=peer_assessment&peer_assessment_publication=' . $publication_id . '&competence=' .$competence->get_id();
            	//dump(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_TAKE_PEER_ASSESSMENT_PUBLICATION, 'peer_assessment_publication' => $publication_id, 'competence' => $competence_id));
            	//Redirect :: url(Translation :: get('TakePublication'), false, (array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_TAKE_PEER_ASSESSMENT_PUBLICATION, 'peer_assessment_publication' => $publication_id, 'competence' => $competence_id)));
            }
            echo implode("\n", $html);
        }
        else
        {
            $html = array();
            $html[] = '<div style="width: 100%; text-align: center;">';
            $html[] = Translation :: get('CompetenceHasNoIndicators');
            $html[] = '</div>';
        }
        $this->parent->get_parent()->display_footer();
    }
}
?>