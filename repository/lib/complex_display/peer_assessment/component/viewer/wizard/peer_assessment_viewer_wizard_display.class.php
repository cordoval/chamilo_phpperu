<?php
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
        
        if($current_page->get_page_number() == 0)
        {
        	$html = array();
            $html[] = '<div style="width: 100%; text-align: center;">';
            $html[] = Translation :: get('NoPeerAssessmentPageAddedToPeerAssessment');
            $html[] = '</div>';
            echo implode("\n", $html);
        }
        elseif ($current_page->get_page_number() == 1)
        {
            $html[] = '<div class="assessment">';
            $html[] = '<h2>' . $this->parent->get_peer_assessment()->get_title() . '</h2>';
            
	        if ($this->parent->get_peer_assessment()->has_description())
	        {
	            $html[] = '<div class="description">';
	            $html[] = $this->parent->get_peer_assessment()->get_description();
	            $html[] = '<div class="clear"></div>';
	            $html[] = '</div>';
	        }

            $html[] = '<br />';            
               
            $html[] = '<h3>' . Translation :: get('Competence') . '</h3>';
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
	        $html[] = '</tbody>';
	        
			// Retrieve competences
	        $competences = $this->parent->get_peer_assessment_page_competences($this->parent->get_peer_assessment());

            foreach($competences as $competence)
            {
            	$url = '';
            	$peer_assessment_id = $this->parent->get_peer_assessment()->get_id();
            	$peer_assessment_publication = $this->parent->get_peer_assessment_publication($peer_assessment_id);
            	$from_date = $peer_assessment_publication->get_from_date();
            	$to_date = $peer_assessment_publication->get_from_date();

            	$title = '<a href="'. $url .'">'.$competence->get_title().'</a>';
            	$take_peer_assessment = '<a href="'. $url .'"><img src="' . Theme :: get_common_image_path() . 'action_next.png' .'" alt=""/></a>';
            	
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

            	
            	// Publication id
            	$peer_assessment_publication_id = Request :: get('peer_assessment_publication');
            	$count = 0;
           		
            	$html[] = '<tr>';
            	$html[] = '<td><img src="'. Theme :: get_common_image_path() . 'content_object/competence.png' .'" alt=""/></td>';
            	$html[] = '<td>'. $title .'</td>';
            	$html[] = '<td>'. $date_message .'</td>';
            	$html[] = '<td>';
            	// Groups
            	$groups = $this->parent->get_peer_assessment_publication_groups($peer_assessment_publication_id)->as_array();
           		$count_groups = sizeof($this->parent->get_peer_assessment_publication_groups($peer_assessment_publication_id)->as_array());
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
            	$html[] = '</td>';
            	$html[] = '<td>';
            	// Users
				$users = $this->parent->get_peer_assessment_publication_users($peer_assessment_publication_id)->as_array();   
            	$count_users = sizeof($this->parent->get_peer_assessment_publication_users($peer_assessment_publication_id)->as_array());
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
           		        	
            	$html[] = '</td>';
            	
            	/*if($competence->isFinished())
            	{
            		$image = 'button_start';
            	}
            	else
            	{*/
            		$image = 'button_cancel';
            	//}
            	
            	$html[] = '<td><img src="' . Theme :: get_common_image_path() . 'buttons/'.$image.'.png' .'" alt="" /></td>';
            	$html[] = '<td>'. $take_peer_assessment .'</td>';
            	$html[] = '</tr>';
            }
            $html[] = '</table>';
            
			
	
            $html[] = '<br />';
            $html[] = '</div>';

            $html[] = '<div>';
            $html[] = $current_page->toHtml();
            $html[] = '</div>';

            $html[] = '<br />';

            $html[] = '<div class="assessment">';
            $html[] = '<br />';

            $html[] = '<div style="width: 100%; text-align: center;">';
            $html[] = $current_page->get_page_number() . ' / ' . $this->parent->get_total_pages();
            $html[] = '</div>';

            $html[] = '</div>';

            echo implode("\n", $html);
        }
        else
        {
        	$html[] = '<div class="assessment">';
            $html[] = '<h2>' . $this->parent->get_peer_assessment()->get_title() . '</h2>';
            
	        if ($this->parent->get_peer_assessment()->has_description())
	        {
	            $html[] = '<div class="description">';
	            $html[] = $this->parent->get_peer_assessment()->get_description();
	            $html[] = '<div class="clear"></div>';
	            $html[] = '</div>';
	        }

            $html[] = '<br />';            
               
            $html[] = '<h3>' . Translation :: get('Indicator') . '</h3>';
	        $html[] = '<table class="data_table">';
	        $html[] = '<thead>';
	        $html[] = '<tr>';
	        $html[] = '<th>'. Translation :: get('Type') .'</th>';
	        $html[] = '<th>' . Translation :: get('Title') . '</th>';
	        $html[] = '<th>' . Translation :: get('Users') . '</th>';
	        $html[] = '<th class="numeric">' . Translation :: get('Finished') . '</th>';
	        $html[] = '<th class="action"></th>';
	        $html[] = '</tr>';
	        $html[] = '</thead>';
	        $html[] = '<tbody>';
	        $html[] = '</tbody>';
	        
			// Retrieve competences
	        $competences = $this->parent->get_peer_assessment_page_competences($this->parent->get_peer_assessment());

            foreach($competences as $competence)
            {	
            	// Retrieve indicators
	            $indicators = $this->parent->get_peer_assessment_page_indicators_via_competence($this->parent->get_peer_assessment(), $competence);
	
	            foreach($indicators as $indicator)
	            {
	            	$html[] = '<tr>';
	            	$html[] = '<td></td>';
	            	$html[] = '<td>'.$indicator->get_title().'</td>';        	
	            	$html[] = '<td></td>';
	            	$html[] = '<td></td>';
	            	$html[] = '<td></td>';
	            	$html[] = '</tr>';
	            }
            }
            $html[] = '</table>';
            
				
            $html[] = '<br />';
            $html[] = '</div>';

            $html[] = '<div>';
            $html[] = $current_page->toHtml();
            $html[] = '</div>';

            $html[] = '<br />';

            $html[] = '<div class="assessment">';
            $html[] = '<br />';

            $html[] = '<div style="width: 100%; text-align: center;">';
            $html[] = $current_page->get_page_number() . ' / ' . $this->parent->get_total_pages();
            $html[] = '</div>';

            $html[] = '</div>';

            echo implode("\n", $html);
        }

        $this->parent->get_parent()->display_footer();

    }
}
?>