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
        $competence_id = Request :: get('competence');
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
	        $competences = $this->get_parent()->get_peer_assessment_page_competences($this->get_parent()->get_peer_assessment());
	        
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
		           			$selected_group = $this->get_parent()->get_group($group_id);
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
		           			$selected_user = $this->get_parent()->get_user($user_id);
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
            
            $html[] = '</tbody>';
            $html[] = '</table>';
            
            $html[] = '<br />';
            $html[] = '</div>';
            
		}

        // Echo's the $html array
        echo implode("\n", $html);        
    }

    /*function get_page_number()
    {
        return $this->page_number;
    }
    
    function get_criteria($criteria_score, $user_id, $form)
    {	
		$form->addElement('select', 'criteria_score_of_user_id_'. $user_id, '', $criteria_score);	
		//$buttons[] = $form->createElement('style_submit_button', 'submit', Translation :: get('Move'), array('class' => 'positive finish'));
		//$form->addGroup($buttons, 'buttons', null, '&nbsp;', false);
		return $form;
    }
    
	function get_feedback($user_id, $form)
    {
		$form->addElement('textarea', 'feedback_to_user_id_'. $user_id, '');	
		return $form;
    }*/
}
?>